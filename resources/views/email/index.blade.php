<x-layouts.base>
    <x-slot name="styles">
        <link href="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/vendor/datatables/buttons.bootstrap4.min.css') }}" rel="stylesheet" />
        <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap4.min.css" rel="stylesheet" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    </x-slot>

    <div class="content">
        <div class="container-fluid">
            <div class="page-title-box">
                <h4 class="page-title">Daftar Email Connected</h4>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active">Gapura - User Manager Hotspot</li>
                </ol>
            </div>

            <div class="row">
                <div class="col-xl-12">
                    <button type="button" class="btn btn-info mb-3" onclick="reload_client()" title="Refresh data">
                        <i class="fas fa-sync-alt"></i>
                    </button>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4 class="header-title mb-4">HOTSPOT EMAIL LOG</h4>

                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center mb-2 mb-md-0">
                                    <label class="me-2 mb-0">Tampilkan:</label>
                                    <select id="customLength" class="form-select form-select-sm w-auto">
                                        <option value="10">10</option>
                                        <option value="25">25</option>
                                        <option value="50">50</option>
                                    </select>
                                </div>
                                <button id="exportCSV" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-file-csv me-1"></i> Export CSV
                                </button>
                            </div>

                            <div class="table-responsive">
                                <table id="tableTamu" class="table table-bordered nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>Aksi</th>
                                            <th>Email</th>
                                            <th>Tanggal Login</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-slot name="scripts">
        <!-- DataTables -->
        <script src="{{ asset('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/datatables/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/vendor/datatables/responsive.bootstrap4.min.js') }}"></script>

        <!-- Export / Buttons -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap4.min.js"></script>
        <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

        <!-- Moment.js -->
        <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>

        <!-- Custom DataTables init -->
        <script>
            const csrfToken = "{{ csrf_token() }}",
                  getTamu = {
                      url: "{{ route('email.show') }}",
                      type: "GET",
                      data: { "_token": csrfToken }
                  },
                  delTamu = "{{ route('email') }}";

            $(document).ready(function () {
                let table = $('#tableTamu').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        {
                            extend: 'csvHtml5',
                            title: 'email_log',
                            exportOptions: { columns: [1, 2] },
                            className: 'd-none'
                        }
                    ],
                    ajax: getTamu,
                    columns: [
                        {
                            data: 'id',
                            render: function (data, type, row) {
                                // console.log(row.email);
                                return `<button type="button" class="badge badge-danger"
                                            data-email="${row.email}" data-id="${row.id}"
                                            onclick="return hapus_tamu(this)">-</button>`;
                            },
                            className: "text-center"
                        },
                        { data: 'email', className: "text-start" },
                        {
                            data: 'created_at',
                            render: function (data) {
                                return moment(data).utcOffset(7).format('DD-MM-YYYY HH:mm');
                            },
                            className: "text-center"
                        }
                    ],
                    responsive: true,
                    lengthChange: false,
                    pageLength: 10,
                    language: {
                        search: "Pencarian:",
                        lengthMenu: "Tampilkan _MENU_ entri",
                        paginate: {
                            first: "Awal", last: "Akhir",
                            next: "→", previous: "←"
                        }
                    }
                });

                $('#customLength').on('change', function () {
                    table.page.len($(this).val()).draw();
                });

                $('#exportCSV').on('click', function () {
                    table.button('.buttons-csv').trigger();
                });
            });

            function reload_client() {
                $('#tableTamu').DataTable().ajax.reload(null, false);
            }

            function hapus_tamu(a) {
                let email = $(a).data('email'),
                    id = $(a).data('id');

                swal({
                    title: 'Apa anda yakin?',
                    text: `Email '${email}' akan dihapus!`,
                    icon: 'warning',
                    buttons: ['Batal', 'Yakin!'],
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: delTamu,
                            type: 'DELETE',
                            data: { _token: csrfToken, del_id: id },
                            beforeSend: () => goBlockUI(true),
                            success: (res) => {
                                $.unblockUI();
                                reload_client();
                                swal(res.title, res.text, res.icon);
                            },
                            error: () => {
                                $.unblockUI();
                                swal('Error', 'Gagal menghapus data', 'error');
                            }
                        });
                    }
                });
            }
        </script>
    </x-slot>
</x-layouts.base>
