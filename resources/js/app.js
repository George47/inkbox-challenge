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
        
        const order_count = $(".content a").length;
        let shuffles = 0;
        if (!order_count || order_count < 20)
        {
            shuffles = 5000;
        } else {
            switch(true) {
                case order_count <= 50 :
                  shuffles = 1000
                  break;
                case order_count <= 100 :
                  shuffles = 500
                  break;
                case order_count <= 150 :
                  shuffles = 200
                  break;
                case order_count <= 200 :
                  shuffles = 100
                  break;
                case order_count <= 250 :
                  shuffles = 50
                  break;
                default:
                  shuffles = 30
              }    
        }

        // shuffles = 1000;

        generatePrint(shuffles);

    });

    $(document).ajaxComplete(()=>{
        $('#newPrint').click(()=>{
            window.location.reload();
        });    
    })

    function generatePrint(shuffles)
    {
        $.ajax({
            type: 'GET',
            url: './prints/generate',
            data: {shuffle: shuffles},
            // statusCode: {
            //     500: function() {
            //         reduceShuffle(shuffles);
            //     }
            // },
            success: function (data)
            {
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

            $('#generatePrint').replaceWith('<button id="newPrint" class="btn btn-outline-secondary directory-buttons">New Print</button>');
            $('.loader').hide();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                reduceShuffle(shuffles);
            }
        });

    }

    function reduceShuffle(shuffles) 
    {
        shuffles = Math.floor(shuffles * 0.7);
        console.log('too many orders, resending request with ' + shuffles + ' shuffles');

        generatePrint(shuffles);
    }


});

