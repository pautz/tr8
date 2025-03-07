<!DOCTYPE html>
<html>
<head>
    <title>TR-808 Drum Machine</title>
  <style>
  body {
    background-color: #6d0505;
    color: #ddd;
    font-family: Arial, sans-serif;
  }

  .container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
  }

  .sound-btn {
    background-color: #222;
    color: #fff;
    width: 50px;
    height: 50px;
    margin: 2px;
    text-align: center;
    line-height: 50px;
    cursor: pointer;
    border: 1px solid #888;
    border-radius: 4px;
    transition: all 0.3s ease;
  }

  .sound-btn:hover {
    background-color: #555;
  }

  .sound-btn.selected {
    background-color: #f00;
    color: #fff;
    border: 1px solid #f00;
  }

  .current-sound {
    border: 2px solid #fff;
    background-color: #444;
  }

  .sequence-step {
    display: inline-block;
    margin: 0 5px 5px 0;
    padding: 10px;
    background-color: #444;
    color: #fff;
    text-align: center;
    border: 1px solid #888;
    border-radius: 4px;
    transition: all 0.3s ease;
  }

  .sequence-step:hover {
    background-color: #666;
  }

  .active-step {
    background-color: #0f0;
    border: 1px solid #0f0;
  }

  .kit-btn {
    margin: 10px;
    padding: 10px 20px;
    background-color: #555;
    color: #fff;
    cursor: pointer;
    border: 1px solid #888;
    border-radius: 4px;
    transition: all 0.3s ease;
  }

  .kit-btn:hover {
    background-color: #777;
  }

  .step-label {
    text-align: center;
    margin-bottom: 10px;
    color: #ccc;
    font-weight: bold;
  }

  @media screen and (max-width: 600px) {
    .sound-btn, .sequence-step, .kit-btn {
      width: 40px;
      height: 40px;
      line-height: 40px;
    }

    .kit-btn {
      padding: 5px 10px;
    }
  }

  .sound-btn:active {
    background-color: #f00;
    color: #fff;
  }

  .sound-btn.selected::before {
    content: none;
  }
</style>

</head>
<body>
<h1>TR-808 Drum Machine</h1>
<div id="kit-selection">
    <h2>Selecionar Kit:</h2>
    <div id="kit-buttons">
        <!-- 9 Kits -->
        <?php for ($i = 1; $i <= 9; $i++): ?>
            <div class="kit-btn" data-kit="<?php echo $i; ?>">Kit <?php echo $i; ?></div>
        <?php endfor; ?>
    </div>
</div>
<div id="sequence">
    <h2>Sequência:</h2>
    <div id="sequence-container">
        <!-- 15 passos -->
        <?php for ($i = 0; $i < 15; $i++): ?>
            <div class="sequence-step" data-step="<?php echo $i; ?>">
                <div class="step-label"><?php echo $i + 1; ?></div>
                <div class="sound-btn" data-step="<?php echo $i; ?>">Som</div>
            </div>
        <?php endfor; ?>
    </div>
</div>
<div>
    <button id="prev-sound">Som Anterior</button>
    <div id="current-sound">Som atual: kick</div>
    <button id="next-sound">Próximo Som</button>
</div>
<label for="bpm">BPM:</label>
<input type="number" id="bpm" value="120">
<button id="play-sequence">Reproduzir Sequência</button>

<button id="prev-pattern">Pattern Anterior</button>
<button id="next-pattern">Próximo Pattern</button>

<audio id="audio-kick" src="http://carlitoslocacoes.com/Oneshot/kick9.wav"></audio>
<audio id="audio-snare" src="http://carlitoslocacoes.com/Oneshot/snare9.wav"></audio>
<audio id="audio-hihat" src="http://carlitoslocacoes.com/Oneshot/hihat9.wav"></audio>
<audio id="audio-clap" src="http://carlitoslocacoes.com/Oneshot/clap9.wav"></audio>
<audio id="audio-bass" src="http://carlitoslocacoes.com/Oneshot/bass9.wav"></audio>

<script>
    const soundButtons = document.querySelectorAll('.sound-btn');
    const kitButtons = document.querySelectorAll('.kit-btn');
    const playButton = document.getElementById('play-sequence');
    const bpmInput = document.getElementById('bpm');
    const prevSoundButton = document.getElementById('prev-sound');
    const nextSoundButton = document.getElementById('next-sound');
    const prevPatternButton = document.getElementById('prev-pattern');
    const nextPatternButton = document.getElementById('next-pattern');
    const currentSoundDisplay = document.getElementById('current-sound');

    let sequence = Array(15).fill(null).map(() => ({
        kick: false,
        snare: false,
        hihat: false,
        clap: false,
        bass: false
    }));
    let currentKit = 9; // Default Kit
    let interval;
    let currentSound = 'kick';
    const sounds = ['kick', 'snare', 'hihat', 'clap', 'bass'];
    let savedPatterns = {
        kick: Array(10).fill(null).map(() => Array(15).fill(null).map(() => ({
            kick: false,
            snare: false,
            hihat: false,
            clap: false,
            bass: false
        }))),
        snare: Array(10).fill(null).map(() => Array(15).fill(null).map(() => ({
            kick: false,
            snare: false,
            hihat: false,
            clap: false,
            bass: false
        }))),
        hihat: Array(10).fill(null).map(() => Array(15).fill(null).map(() => ({
            kick: false,
            snare: false,
            hihat: false,
            clap: false,
            bass: false
        }))),
        clap: Array(10).fill(null).map(() => Array(15).fill(null).map(() => ({
            kick: false,
            snare: false,
            hihat: false,
            clap: false,
            bass: false
        }))),
        bass: Array(10).fill(null).map(() => Array(15).fill(null).map(() => ({
            kick: false,
            snare: false,
            hihat: false,
            clap: false,
            bass: false
        })))
    };
    let currentPattern = {
        kick: 0,
        snare: 0,
        hihat: 0,
        clap: 0,
        bass: 0
    };
    const maxPatterns = 10; // Maximum number of patterns to save

    // Function to toggle sound on a specific step and save pattern
    function toggleSound(step) {
        sequence[step][currentSound] = !sequence[step][currentSound];
        document.querySelector(`[data-step="${step}"] .sound-btn`).classList.toggle('selected', sequence[step][currentSound]);
        
        // Save the current pattern
        savedPatterns[currentSound][currentPattern[currentSound]] = [...sequence];
    }

    // Event listener to toggle sound on button click
    soundButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            const step = parseInt(event.target.dataset.step, 10);
            toggleSound(step);
        });
    });

    // Function to change the instrument names and audio sources based on the selected kit
    function changeKit(kitNumber) {
        currentKit = kitNumber;
        document.getElementById('audio-kick').src = `http://carlitoslocacoes.com/Oneshot/kick${kitNumber}.wav`;
        document.getElementById('audio-snare').src = `http://carlitoslocacoes.com/Oneshot/snare${kitNumber}.wav`;
        document.getElementById('audio-hihat').src = `http://carlitoslocacoes.com/Oneshot/hihat${kitNumber}.wav`;
        document.getElementById('audio-clap').src = `http://carlitoslocacoes.com/Oneshot/clap${kitNumber}.wav`;
        document.getElementById('audio-bass').src = `http://carlitoslocacoes.com/Oneshot/bass${kitNumber}.wav`;
    }

    // Event listener to change kit on button click
    kitButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            const kitNumber = parseInt(event.target.dataset.kit, 10);
            changeKit(kitNumber);
        });
    });

    // Function to play the sequence
    function playSequence() {
        const bpm = parseInt(bpmInput.value, 10);
        const intervalTime = (60 / bpm) * 1000 / 4; // Adjust timing as needed

        let stepIndex = 0;

        function playStep() {
            // Ensuring no overlapping sounds
            const kick = document.getElementById('audio-kick');
            const snare = document.getElementById('audio-snare');
            const hihat = document.getElementById('audio-hihat');
            const clap = document.getElementById('audio-clap');
            const bass = document.getElementById('audio-bass');

            [kick, snare, hihat, clap, bass].forEach(audio => {
                audio.pause();
                audio.currentTime = 0;
            });

            const currentStep = {
                kick: savedPatterns.kick[currentPattern.kick][stepIndex],
                snare: savedPatterns.snare[currentPattern.snare][stepIndex],
                hihat: savedPatterns.hihat[currentPattern.hihat][stepIndex],
                clap: savedPatterns.clap[currentPattern.clap][stepIndex],
                bass: savedPatterns.bass[currentPattern.bass][stepIndex]
            };

            sounds.forEach(sound => {
                if (currentStep[sound][sound]) {
                    const audio = document.getElementById(`audio-${sound}`);
                    audio.currentTime = 0;
                    audio.play();
                }
            });

            // Atualizar a marcação visual
            document.querySelectorAll('.sound-btn').forEach(btn => btn.classList.remove('active-step'));
            document.querySelector(`[data-step="${stepIndex}"] .sound-btn`).classList.add('active-step');

            stepIndex = (stepIndex + 1) % sequence.length;
        }

        clearInterval(interval);
        interval = setInterval(playStep, intervalTime);
    }

    // Function to change the current sound and save patterns
    function changeSound(direction) {
        const currentIndex = sounds.indexOf(currentSound);

        // Save the current pattern
        savedPatterns[currentSound][currentPattern[currentSound]] = [...sequence];

        if (direction === 'next') {
            currentSound = sounds[(currentIndex + 1) % sounds.length];
        } else if (direction === 'prev') {
            currentSound = sounds[(currentIndex - 1 + sounds.length) % sounds.length];
        }

        // Load the saved pattern for the new sound
        sequence = [...savedPatterns[currentSound][currentPattern[currentSound]]];

        // Clear and set light indicators
        soundButtons.forEach(button => {
            const step = parseInt(button.dataset.step, 10);
            button.classList.toggle('selected', sequence[step][currentSound]);
        });

        // Highlight which sound is currently selected
        document.querySelectorAll(`[data-step] .sound-btn`).forEach(btn => {
            if (sequence[parseInt(btn.dataset.step)][currentSound]) {
                btn.classList.add('current-sound');
            } else {
                btn.classList.remove('current-sound');
            }
        });

        // Update the display of current sound
        currentSoundDisplay.textContent = `Som atual: ${currentSound}`;
    }

    // Event listeners for sound change buttons
    nextSoundButton.addEventListener('click', () => changeSound('next'));
    prevSoundButton.addEventListener('click', () => changeSound('prev'));

    // Function to switch to the next or previous pattern
    function changePattern(direction) {
        const sound = currentSound;

        // Save the current pattern for the current sound
        savedPatterns[sound][currentPattern[sound]] = [...sequence];

        if (direction === 'next') {
            currentPattern[sound] = (currentPattern[sound] + 1) % maxPatterns;
        } else if (direction === 'prev') {
            currentPattern[sound] = (currentPattern[sound] - 1 + maxPatterns) % maxPatterns;
        }

        // Load the saved pattern for the new pattern
        sequence = JSON.parse(JSON.stringify(savedPatterns[sound][currentPattern[sound]]));

        // Update the button states for the new pattern
        soundButtons.forEach(button => {
            const step = parseInt(button.dataset.step, 10);
            button.classList.toggle('selected', sequence[step][sound]);
        });
    }

    // Event listeners for pattern change buttons
    nextPatternButton.addEventListener('click', () => changePattern('next'));
    prevPatternButton.addEventListener('click', () => changePattern('prev'));

    // Initial setup
    changeKit(currentKit);
    changeSound('next'); // Load the initial sound pattern
    playButton.addEventListener('click', playSequence); // Play the sequence when the play button is clicked
</script>
</body>
</html>
