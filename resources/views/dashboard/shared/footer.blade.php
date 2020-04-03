<footer class="c-footer">
  <div><a href="http://cyberelectra.co.id">TOOS</a>&trade; &copy; {{date('Y')}} Cyber Electra.</div>
  <div class="ml-auto">Powered by&nbsp;<a href="https://coreui.io/">CoreUI</a></div>
</footer>
<div id="modal_content" class="modal fade" role="dialog">
  <div class="modal-dialog" role="document">
  </div>
</div>
<script>
  var initDestroyTimeOutPace = function() {
    var counter = 0;

    var refreshIntervalId = setInterval( function(){
        var progress; 

        if( typeof $( '.pace-progress' ).attr( 'data-progress-text' ) !== 'undefined' ) {
            progress = Number( $( '.pace-progress' ).attr( 'data-progress-text' ).replace("%" ,'') );
        }

        if( progress === 99 ) {
            counter++;
        }

        if( counter > 50 ) {
            clearInterval(refreshIntervalId);
            Pace.stop();
        }
    }, 100);
  }
  initDestroyTimeOutPace();
</script>