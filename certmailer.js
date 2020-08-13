
var i = 0;
var successcount=0;
var val = 0;
var startcert;
var endcert;
$( document ).ready(function() {
    $("#submit").click(function (e) {
        e.preventDefault();
        clearprevious();
        startcert = $('#startcert').val();
        endcert = $('#endcert').val();
        var title = $('#title').val();
        var on = $('#on').val();
        var by = $('#by').val();
        var message = $('#message').val();
        var external = $('#external').is(':checked');
        console.log(external);
        if (message != ""){}
        for (var counter = startcert; counter <= endcert; counter++) { 
            $.ajax({
            url: 'certmailer2.php',
            // async: false,
            type: 'POST',
            data: { "counter_id" : counter, "message" : message, "title" : title, "on" : on, "by" : by, "external" : external,  },
            success: function (message)
            {
                successcount++;
                $("#successmessage").append(message);
            },
            error: function (errormessage)
            {
                $("#errormessage").text(errormessage);
            }
            });
        }
    });
});
function move() {
    if (i == 0) {
      i = 1;
      var elem = document.getElementById("myBar");
      var width = 1;
      var id = setInterval(frame, 10);
      function frame() {
        if (width >= 100) {
          clearInterval(id);
          i = 0;
        } else {
          width=(successcount*100)/(endcert-startcert+1);
          elem.style.width = width + "%";
        }
      }
    }
  }

  function clearprevious() {
    i = 0;
    successcount=0;
    $("#successmessage").html("");
  }


