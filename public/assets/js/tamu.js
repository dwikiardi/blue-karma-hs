$(document).ready(function () {
    init_tamu();
});

function init_tamu() {
    let table = $('#tableTamu').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'csvHtml5',
                title: 'email_log',
                exportOptions: {
                    columns: [1, 2]
                },
                className: 'd-none'
            }
        ],
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        responsive: true,
        lengthChange: false,
        pageLength: 10,
        language: {
            search: '<span>Pencarian:</span> _INPUT_',
            searchPlaceholder: 'Cari email...',
            lengthMenu: 'Tampilkan _MENU_ entri',
            paginate: {
                first: 'Awal',
                last: 'Akhir',
                next: '→',
                previous: '←'
            }
        },
        ajax: getTamu,
        columns: [
            {
                data: 'mac',
                render: function (data, type, row) {
                    return `<button type="button" class="badge badge-danger"
                        data-name="${data}" data-desc="${row.comment ?? ''}"
                        onclick="return hapus_client(this)">-</button>`;
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
        ]
    });

    $('#customLength').on('change', function () {
        table.page.len($(this).val()).draw();
    });

    $('#exportCSV').on('click', function () {
        table.button('.buttons-csv').trigger();
    });
}

function reload_client() {
    $('#tableTamu').DataTable().ajax.reload(null, false);
}

function hapus_client(a) {
    let name = $(a).data('name'),
        desc = $(a).data('desc');

    swal({
        title: 'Apa anda yakin?',
        text: `Client '${name}' akan dihapus!`,
        icon: 'warning',
        buttons: ['Batal', 'Yakin!'],
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: delClient,
                type: 'DELETE',
                data: { _token: csrfToken, del_id: name, del_desc: desc },
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
