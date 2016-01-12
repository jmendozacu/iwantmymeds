$j(document).ready(function() {
    var div = $j('div.call-ajax.my-prescription');
    var li = $j('div.call-ajax.my-prescription ul li');
    var base_url = $j('div.base-url');
    var insert = $j('article .std');
    $j(li).click(function(){
        var param = $j(this).attr('id');
        // Click to link Repeat Prescription
        if(param == 4){
            $j.ajax({
                url: $j(base_url).text() + 'myprescription/registerrepeat',
                success: function(result){
                    insert.html(result);
                    $j('.attribute').val(param);
                }
            });
            return false;
        }
    });

    // Call to form Register Repeat Prescription
    $j('body').delegate('.action-register a.register-repeat-prescription','click',function(){
        var url = $j('.action-register a.register-repeat-prescription').attr('href');
        $j.ajax({
            url: url,
            success: function(result){
                $j('.action-register').html(result);
            }
        });
        return false;
    });
});
