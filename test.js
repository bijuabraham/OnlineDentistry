
var i = 0;
var successcount=0;
var val = 0;
$( document ).ready(function() {
    $("#submit").click(function (e) {
        e.preventDefault();
        clearprevious();
        val = $('#counter_id').val();
        for (var counter = 0; counter <= val; counter++) { 
            $.ajax({
            url: 'certmailer2.php',
            // async: false,
            type: 'POST',
            data: { "counter_id" : counter },
            success: function (message)
            {
                $("#successcount").text(successcount);
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
          width=(successcount*100)/val;
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


