// require('./bootstrap');

$(document).ready(function(){
    console.log(window.location.href);
    $('#generatePrint').click(() =>{
        console.log('generating ... ');

        $.get('./prints/generate', function(data) {
            let matrix = '';
            for (let i = 0; i < data.length; i++)
            {
                for (let j = 0; j < data[0].length; j++)
                {
                    matrix += data[i][j] + '\xa0\xa0\xa0\xa0\xa0\xa0\xa0';

                    if (j == data[0].length - 1)
                    {
                        matrix += '<br><br>';
                    }
                }
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

