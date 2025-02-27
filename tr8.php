<!DOCTYPE html>
<html>
<head>
    <title>TR-808 Drum Machine</title>
    <style>
        .sound-btn {
            display: inline-block;
            width: 50px;
            height: 50px;
            margin: 2px;
            background-color: #ccc;
            text-align: center;
            line-height: 50px;
            cursor: pointer;
        }
        .selected {
            background-color: #f00;
        }
        .sequence-step {
            display: inline-block;
            margin-right: 5px;
        }
        .step-label {
            text-align: center;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <h1>TR-808 Drum Machine</h1>
    <div id="sequence">
        <h2>Sequência:</h2>
        <div id="sequence-container">
            <!-- 15 tempos -->
            <?php for ($i = 0; $i < 15; $i++): ?>
            <div class="sequence-step" data-step="<?php echo $i; ?>">
                <div class="step-label"><?php echo $i + 1; ?></div>
                <div class="sound-btn" data-step="<?php echo $i; ?>" data-sound="kick">Kick</div>
                <div class="sound-btn" data-step="<?php echo $i; ?>" data-sound="snare">Snare</div>
                <div class="sound-btn" data-step="<?php echo $i; ?>" data-sound="hihat">Hi-hat</div>
                <div class="sound-btn" data-step="<?php echo $i; ?>" data-sound="clap">Clap</div>
                <div class="sound-btn" data-step="<?php echo $i; ?>" data-sound="bass">Bass</div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
    <label for="bpm">BPM:</label>
    <input type="number" id="bpm" value="120">
    <button id="play-sequence">Reproduzir Sequência</button>
    <audio id="audio-kick" src="http://carlitoslocacoes.com/Oneshot/kick9.wav"></audio>
    <audio id="audio-snare" src="http://carlitoslocacoes.com/Oneshot/snare9.wav"></audio>
    <audio id="audio-hihat" src="http://carlitoslocacoes.com/Oneshot/hihat9.wav"></audio>
    <audio id="audio-clap" src="http://carlitoslocacoes.com/Oneshot/clap9.wav"></audio>
    <audio id="audio-bass" src="http://carlitoslocacoes.com/Oneshot/bass9.wav"></audio>
    <script>
        const soundButtons = document.querySelectorAll('.sound-btn');
        const playButton = document.getElementById('play-sequence');
        const bpmInput = document.getElementById('bpm');
        let sequence = Array(15).fill(null).map(() => ({ kick: false, snare: false, hihat: false, clap: false, bass: false }));
        let interval;

        // Function to toggle sound on a specific step
        function toggleSound(step, sound) {
            sequence[step][sound] = !sequence[step][sound];
            document.querySelectorAll(`[data-step="${step}"][data-sound="${sound}"]`)[0].classList.toggle('selected');
        }

        // Event listener to toggle sound on button click
        soundButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                const step = parseInt(event.target.dataset.step, 10);
                const sound = event.target.dataset.sound;
                toggleSound(step, sound);
            });
        });

        // Function to play the sequence
        function playSequence() {
            const bpm = parseInt(bpmInput.value, 10);
            const intervalTime = (60 / bpm) * 1000 / 4; // Adjust timing as needed
            let stepIndex = 0;

            function playStep() {
                const step = sequence[stepIndex];
                // Ensuring no overlapping sounds
                if (!document.getElementById('audio-kick').paused) document.getElementById('audio-kick').pause();
                if (!document.getElementById('audio-snare').paused) document.getElementById('audio-snare').pause();
                if (!document.getElementById('audio-hihat').paused) document.getElementById('audio-hihat').pause();
                if (!document.getElementById('audio-clap').paused) document.getElementById('audio-clap').pause();
                if (!document.getElementById('audio-bass').paused) document.getElementById('audio-bass').pause();

                if (step.kick) {
                    const kick = document.getElementById('audio-kick');
                    kick.currentTime = 0;
                    kick.play();
                }
                if (step.snare) {
                    const snare = document.getElementById('audio-snare');
                    snare.currentTime = 0;
                    snare.play();
                }
                if (step.hihat) {
                    const hihat = document.getElementById('audio-hihat');
                    hihat.currentTime = 0;
                    hihat.play();
                }
                if (step.clap) {
                    const clap = document.getElementById('audio-clap');
                    clap.currentTime = 0;
                    clap.play();
                }
                if (step.bass) {
                    const bass = document.getElementById('audio-bass');
                    bass.currentTime = 0;
                    bass.play();
                }
                stepIndex = (stepIndex + 1) % sequence.length;
            }

            clearInterval(interval);
            interval = setInterval(playStep, intervalTime);
        }

        playButton.addEventListener('click', playSequence);
    </script>
</body>
</html>
