// require('./bootstrap');

$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    console.log(window.location.href);
    $('#generatePrint').click(() =>{
        console.log('generating ... ');

        $.get('./prints/generate', function(data) {
            let matrix = '';

            for (let print = 0; print < data.length; print++)
            {
                matrix += '<strong> Sheet ' + (print + 1) + '</strong><br><br>';
                let print_details = data[print];
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
            }        
            $('.print-report').replaceWith('<p>' + matrix + '</p>');
            $('#generatePrint').replaceWith('<button id="newPrint" class="btn btn-outline-secondary directory-buttons">New Print</button>');
        })
    });

    $(document).ajaxComplete(()=>{
        $('#newPrint').click(()=>{
            window.location.reload();
        });    
    })
});

