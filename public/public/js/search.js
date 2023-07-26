
		$("#myInput").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#myTable a").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });