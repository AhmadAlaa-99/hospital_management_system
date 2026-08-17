$(function(e) {
	var hmsDtLang = {
		searchPlaceholder: 'بحث...',
		sSearch: '',
		lengthMenu: 'عرض _MENU_',
		info: 'عرض _START_ إلى _END_ من _TOTAL_',
		infoEmpty: 'لا توجد بيانات',
		infoFiltered: '(من _MAX_)',
		zeroRecords: 'لا توجد نتائج',
		paginate: { first: 'الأول', last: 'الأخير', next: 'التالي', previous: 'السابق' },
		buttons: {
			copy: 'نسخ',
			colvis: 'إظهار الأعمدة',
			print: 'طباعة',
			excel: 'Excel',
			pdf: 'PDF'
		}
	};

	if ($('#example').length && !$.fn.DataTable.isDataTable('#example')) {
		var table = $('#example').DataTable({
			lengthChange: false,
			scrollX: true,
			autoWidth: false,
			buttons: [ 'copy', 'excel', 'pdf', 'colvis' ],
			responsive: false,
			language: hmsDtLang,
			columnDefs: [
				{ orderable: false, targets: [1, -2] }
			]
		});
		table.buttons().container().appendTo('#example_wrapper .col-md-6:eq(0)');
	}

	if ($('#example1').length && !$.fn.DataTable.isDataTable('#example1')) {
		$('#example1').DataTable({ language: hmsDtLang });
	}

	if ($('#example2').length && !$.fn.DataTable.isDataTable('#example2')) {
		$('#example2').DataTable({ responsive: true, language: hmsDtLang });
	}

	if ($('#example-delete').length && !$.fn.DataTable.isDataTable('#example-delete')) {
		var tableDel = $('#example-delete').DataTable({ responsive: true, language: hmsDtLang });
		$('#example-delete tbody').on('click', 'tr', function () {
			if ($(this).hasClass('selected')) {
				$(this).removeClass('selected');
			} else {
				tableDel.$('tr.selected').removeClass('selected');
				$(this).addClass('selected');
			}
		});
		$('#button').click(function () {
			tableDel.row('.selected').remove().draw(false);
		});
	}

	if ($('#example-1').length && !$.fn.DataTable.isDataTable('#example-1')) {
		$('#example-1').DataTable({
			responsive: true,
			language: hmsDtLang,
			responsive: {
				details: {
					display: $.fn.dataTable.Responsive.display.modal({
						header: function (row) {
							var data = row.data();
							return 'تفاصيل ' + data[0] + ' ' + data[1];
						}
					}),
					renderer: $.fn.dataTable.Responsive.renderer.tableAll({
						tableClass: 'table border mb-0'
					})
				}
			}
		});
	}
});
