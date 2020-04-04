function initProgressBar() {
    var player = document.getElementById('player');
    var length = player.duration
    var current_time = player.currentTime;

    // calculate total length of value
    var totalLength = calculateTotalValue(length)
    document.getElementById("end-time").innerHTML = totalLength;

    // calculate current value time
    var currentTime = calculateCurrentValue(current_time);
    document.getElementById("start-time").innerHTML = currentTime;

    var progressbar = document.getElementById('seek-obj');
    progressbar.value = (player.currentTime / player.duration);
    //progressbar.addEventListener("click", seek); matikan fungsi scroll timer

    if (player.currentTime == player.duration) {
        document.getElementById('play-button').className = "";
    }
    function seek(event) {
        var percent = event.offsetX / this.offsetWidth;
        player.currentTime = percent * player.duration;
        progressbar.value = percent / 100;
    }
};
function initPlayers(num) {
    // pass num in if there are multiple audio players e.g 'player' + i
    for (var i = 0; i < num; i++) {
        (function () {
            // Variables
            // ----------------------------------------------------------
            // audio embed object
            var playerContainer = document.getElementById('player-container'),
                player = document.getElementById('player'),
                isPlaying = false,
                playBtn = document.getElementById('play-button');
            // Controls Listeners
            // ----------------------------------------------------------
            if (playBtn != null) {
                playBtn.addEventListener('click', function () {
                    togglePlay($(this));
                });
            }
            // Controls & Sounds Methods
            // ----------------------------------------------------------
            var count = 0;
            function togglePlay(a) {
                count += 1;
                if (count > 3) {
                    console.log('disable');
                    return false;
                }
                var icon = $(a).children()[0];
                if (player.paused === false) {
                    player.pause();
                    isPlaying = false;
                    $(icon).addClass('cil-media-play').removeClass('cil-media-pause');
                    //document.getElementById('play-button').className = "pause";
                } else {
                    player.play();
                    $(icon).removeClass('cil-media-play').addClass('cil-media-pause');
                    //document.getElementById('play-button').className = "play";
                    isPlaying = true;
                }
            }
        }());
    }
}
function calculateTotalValue(length) {
    var minutes = Math.floor(length / 60),
        seconds_int = length - minutes * 60,
        seconds_str = seconds_int.toString(),
        seconds = seconds_str.substr(0, 2),
        time = minutes + ':' + seconds
    return time;
}
function calculateCurrentValue(currentTime) {
    var current_hour = parseInt(currentTime / 3600) % 24,
        current_minute = parseInt(currentTime / 60) % 60,
        current_seconds_long = currentTime % 60,
        current_seconds = current_seconds_long.toFixed(),
        current_time = (current_minute < 10 ? "0" + current_minute : current_minute) + ":" + (current_seconds < 10 ? "0" + current_seconds : current_seconds);

    return current_time;
}