// require('./bootstrap');

$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    console.log(window.location.href);
    $('#generatePrint').click(() =>{
        $('#generatePrint').attr("disabled", "");
        $('.loader').show();
        
        console.log('generating ... ');

        // handle 500 server error, and reduce the shuffle count
        // data = {};
        // $.ajax({

        // })

        $.get('./prints/generate', function(data) {
            $('.print-report-sheets').empty();        

            $('.print-report').prepend('<p><strong>Unused '+ data.unused +' </strong></p>')

            for (let print = 0; print < data.sheet.length; print++)
            {
                let matrix = '';
                matrix += '<strong> Sheet ' + (print + 1) + '</strong><br><br>';
                let print_details = data.sheet[print];
                for (let i = 0; i < print_details.length; i++)
                {
                    for (let j = 0; j < print_details[0].length; j++)
                    {
                        matrix += print_details[i][j] + '\xa0\xa0\xa0\xa0\xa0\xa0\xa0';

                        if (j == print_details[0].length - 1)
                        {
                            matrix += '<br><br>';
                        }
                    }
                }
                matrix += '<br><br>';

                $('.print-report-sheets').append(
                    '   <div class="col-sm">\
                            ' + matrix + '\
                        </div>\
                    '
                );
    
            }
            // $('.print-report').append('<p>' + matrix + '</p>');
            $('#generatePrint').replaceWith('<button id="newPrint" class="btn btn-outline-secondary directory-buttons">New Print</button>');

            $('.loader').hide();
        })
    });

    $(document).ajaxComplete(()=>{
        $('#newPrint').click(()=>{
            window.location.reload();
        });    
    })
});

