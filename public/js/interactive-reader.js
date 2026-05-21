document.addEventListener('alpine:init', () => {
    Alpine.data('interactiveReader', (text, enabled, preferredVoice) => ({
        text: text,
        enabled: enabled,
        preferredVoice: preferredVoice,
        words: [],
        voices: [],
        selectedVoice: null,
        isDragging: false,
        activeWordIndex: -1,

        init() {
            this.words = this.text.split(/\s+/).filter(w => w.length > 0);

            const loadVoices = () => {
                this.voices = window.speechSynthesis.getVoices();
                if (this.voices.length > 0) {
                    this.determineVoice();
                }
            };

            loadVoices();
            if (speechSynthesis.onvoiceschanged !== undefined) {
                speechSynthesis.onvoiceschanged = loadVoices;
            }
        },

        determineVoice() {
            if (this.preferredVoice) {
                const voice = this.voices.find(v => v.name === this.preferredVoice);
                if (voice) {
                    this.selectedVoice = voice;
                    return;
                }
            }

            const indonesian = this.voices.find(v => v.lang.startsWith('id-') || v.lang.startsWith('in-'));
            if (indonesian) {
                this.selectedVoice = indonesian;
                return;
            }

            this.selectedVoice = this.voices[0];
        },

        readWord(word, index) {
            if (!this.enabled || !window.speechSynthesis) return;

            this.activeWordIndex = index;

            if (navigator.vibrate) {
                navigator.vibrate(50);
            }

            window.speechSynthesis.cancel();
            const utterance = new SpeechSynthesisUtterance(word);

            if (this.selectedVoice) {
                utterance.voice = this.selectedVoice;
            }

            utterance.rate = 0.9;
            utterance.pitch = 1;

            utterance.onend = () => {
                if (this.activeWordIndex === index && !this.isDragging) {
                    this.activeWordIndex = -1;
                }
            };

            window.speechSynthesis.speak(utterance);
        },

        handleMove(e) {
            if (!this.isDragging) return;

            let clientX, clientY;
            if (e.touches && e.touches.length > 0) {
                clientX = e.touches[0].clientX;
                clientY = e.touches[0].clientY;
            } else {
                clientX = e.clientX;
                clientY = e.clientY;
            }

            const element = document.elementFromPoint(clientX, clientY);

            if (element && element.dataset.wordIndex !== undefined) {
                const index = parseInt(element.dataset.wordIndex, 10);
                if (index !== this.activeWordIndex) {
                    this.readWord(this.words[index], index);
                }
            }
        },

        startDrag() {
            this.isDragging = true;
        },

        stopDrag() {
            this.isDragging = false;
            this.activeWordIndex = -1;
            window.speechSynthesis.cancel();
        }
    }));
});
