<!DOCTYPE html>
<html>
<head>
    <title>Clubone15 Drum Machine</title>
    <style>
        .sound-btn { display: inline-block; width: 50px; height: 50px; margin: 2px; background-color: #ccc; text-align: center; line-height: 50px; cursor: pointer; }
        .selected { background-color: #f00; }
        .current-sound { border: 2px solid #000; }
        .sequence-step { display: inline-block; margin-right: 5px; }
        .step-label { text-align: center; margin-bottom: 5px; }
        .kit-btn { margin: 5px; padding: 5px; background-color: #ccc; cursor: pointer; }
        .pattern-display { display: none; margin-top: 20px; }
    </style>
</head>
<body>
<h1>Clubone15 Drum Machine</h1>
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
<button id="show-patterns">Mostrar Patterns</button>

<audio id="audio-kick" src="http://carlitoslocacoes.com/Oneshot/kick9.wav"></audio>
<audio id="audio-snare" src="http://carlitoslocacoes.com/Oneshot/snare9.wav"></audio>
<audio id="audio-hihat" src="http://carlitoslocacoes.com/Oneshot/hihat9.wav"></audio>
<audio id="audio-clap" src="http://carlitoslocacoes.com/Oneshot/clap9.wav"></audio>
<audio id="audio-bass" src="http://carlitoslocacoes.com/Oneshot/bass9.wav"></audio>

<div id="pattern-display" class="pattern-display"></div>
<script>
const soundButtons = document.querySelectorAll('.sound-btn');
const kitButtons = document.querySelectorAll('.kit-btn');
const playButton = document.getElementById('play-sequence');
const bpmInput = document.getElementById('bpm');
const prevSoundButton = document.getElementById('prev-sound');
const nextSoundButton = document.getElementById('next-sound');
const showPatternsButton = document.getElementById('show-patterns');
const patternDisplay = document.getElementById('pattern-display');
const currentSoundDisplay = document.getElementById('current-sound');

let sequence = Array(15).fill(null).map(() => ({ kick: false, snare: false, hihat: false, clap: false, bass: false }));
let currentKit = 9; // Default Kit
let interval;
let currentSound = 'kick';
const sounds = ['kick', 'snare', 'hihat', 'clap', 'bass'];
let savedPatterns = { kick: [...sequence], snare: [...sequence], hihat: [...sequence], clap: [...sequence], bass: [...sequence] };

// Function to toggle sound on a specific step
function toggleSound(step) {
  sequence[step][currentSound] = !sequence[step][currentSound];
  document.querySelector(`[data-step="${step}"] .sound-btn`).classList.toggle('selected', sequence[step][currentSound]);
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
    const step = sequence[stepIndex];

    // Ensuring no overlapping sounds
    const kick = document.getElementById('audio-kick');
    const snare = document.getElementById('audio-snare');
    const hihat = document.getElementById('audio-hihat');
    const clap = document.getElementById('audio-clap');
    const bass = document.getElementById('audio-bass');

    [kick, snare, hihat, clap, bass].forEach(audio => audio.pause());

    if (step.kick) {
      kick.currentTime = 0;
      kick.play();
    }
    if (step.snare) {
      snare.currentTime = 0;
      snare.play();
    }
    if (step.hihat) {
      hihat.currentTime = 0;
      hihat.play();
    }
    if (step.clap) {
      clap.currentTime = 0;
      clap.play();
    }
    if (step.bass) {
      bass.currentTime = 0;
      bass.play();
    }

    stepIndex = (stepIndex + 1) % sequence.length;
  }

  clearInterval(interval);
  interval = setInterval(playStep, intervalTime);
}

// Function to change the current sound and save patterns
function changeSound(direction) {
  const currentIndex = sounds.indexOf(currentSound);

  // Save the current pattern
  savedPatterns[currentSound] = [...sequence];

  if (direction === 'next') {
    currentSound = sounds[(currentIndex + 1) % sounds.length];
  } else if (direction === 'prev') {
    currentSound = sounds[(currentIndex - 1 + sounds.length) % sounds.length];
  }

  // Load the saved pattern for the new sound
  sequence = [...savedPatterns[currentSound]];

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

// Function to display all patterns as buttons
function displayPatterns() {
  const patterns = sounds.map(sound => {
    const steps = savedPatterns[sound].map((step, index) => {
      return `<button class="pattern-step ${step[sound] ? 'selected' : ''}" data-sound="${sound}" data-step="${index}">${index + 1}</button>`;
    }).join('');

    return `<h3>${sound}:</h3><div class="pattern-steps">${steps}</div>`;
  }).join('');

  patternDisplay.innerHTML = patterns;
  patternDisplay.style.display = 'block';

  // Event listeners for the pattern buttons
  document.querySelectorAll('.pattern-step').forEach(button => {
    button.addEventListener('click', (event) => {
      const sound = event.target.dataset.sound;
      const step = parseInt(event.target.dataset.step, 10);
      savedPatterns[sound][step][sound] = !savedPatterns[sound][step][sound];
      event.target.classList.toggle('selected', savedPatterns[sound][step][sound]);
    });
  });
}

// Event listener for sound change buttons
nextSoundButton.addEventListener('click', () => changeSound('next'));
prevSoundButton.addEventListener('click', () => changeSound('prev'));
playButton.addEventListener('click', playSequence);
showPatternsButton.addEventListener('click', displayPatterns);
</script>
<script>
    const soundButtons = document.querySelectorAll('.sound-btn');
    const kitButtons = document.querySelectorAll('.kit-btn');
    const playButton = document.getElementById('play-sequence');
    const bpmInput = document.getElementById('bpm');
    const prevSoundButton = document.getElementById('prev-sound');
    const nextSoundButton = document.getElementById('next-sound');
    const showPatternsButton = document.getElementById('show-patterns');
    const patternDisplay = document.getElementById('pattern-display');
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
        kick: [...sequence],
        snare: [...sequence],
        hihat: [...sequence],
        clap: [...sequence],
        bass: [...sequence]
    };

    // Function to toggle sound on a specific step
    function toggleSound(step) {
        sequence[step][currentSound] = !sequence[step][currentSound];
        document.querySelector(`[data-step="${step}"] .sound-btn`).classList.toggle('selected', sequence[step][currentSound]);
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
            const step = sequence[stepIndex];

            // Ensuring no overlapping sounds
            const kick = document.getElementById('audio-kick');
            const snare = document.getElementById('audio-snare');
            const hihat = document.getElementById('audio-hihat');
            const clap = document.getElementById('audio-clap');
            const bass = document.getElementById('audio-bass');

            [kick, snare, hihat, clap, bass].forEach(audio => audio.pause());

            if (step.kick) { kick.currentTime = 0; kick.play(); }
            if (step.snare) { snare.currentTime = 0; snare.play(); }
            if (step.hihat) { hihat.currentTime = 0; hihat.play(); }
            if (step.clap) { clap.currentTime = 0; clap.play(); }
            if (step.bass) { bass.currentTime = 0; bass.play(); }

            stepIndex = (stepIndex + 1) % sequence.length;
        }

        clearInterval(interval);
        interval = setInterval(playStep, intervalTime);
    }

    // Function to change the current sound and save patterns
    function changeSound(direction) {
        const currentIndex = sounds.indexOf(currentSound);

        // Save the current pattern
        savedPatterns[currentSound] = [...sequence];

        if (direction === 'next') {
            currentSound = sounds[(currentIndex + 1) % sounds.length];
        } else if (direction === 'prev') {
            currentSound = sounds[(currentIndex - 1 + sounds.length) % sounds.length];
        }

        // Load the saved pattern for the new sound
        sequence = [...savedPatterns[currentSound]];

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

        // Function to display all patterns
    function displayPatterns() {
        const patterns = sounds.map(sound => {
            return `<h3>${sound}:</h3><pre>${JSON.stringify(savedPatterns[sound], null, 2)}</pre>`;
        }).join('');
        patternDisplay.innerHTML = patterns;
        patternDisplay.style.display = 'block';
    }

    // Event listener for sound change buttons
    nextSoundButton.addEventListener('click', () => changeSound('next'));
    prevSoundButton.addEventListener('click', () => changeSound('prev'));
    playButton.addEventListener('click', playSequence);
    showPatternsButton.addEventListener('click', displayPatterns);

</script>
</body>
</html>
