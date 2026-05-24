document.addEventListener('alpine:init', () => {
    Alpine.data('interactiveReader', (text, enabled, preferredVoice) => ({
        text: text,
        enabled: enabled,
        preferredVoice: preferredVoice,
        voices: [],
        selectedVoice: null,
        isPlaying: false,

        init() {
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

            // Handle when speech ends naturally
            this.handleEnd = () => {
                this.isPlaying = false;
            };
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

        togglePlay() {
            if (!this.enabled || !window.speechSynthesis) return;

            if (this.isPlaying) {
                this.stop();
            } else {
                this.play();
            }
        },

        play() {
            if (!this.enabled || !window.speechSynthesis) return;

            window.speechSynthesis.cancel();

            // Strip simple html tags if any exist in the text to avoid reading HTML aloud
            const plainText = this.text.replace(/<[^>]*>?/gm, '');

            const utterance = new SpeechSynthesisUtterance(plainText);

            if (this.selectedVoice) {
                utterance.voice = this.selectedVoice;
            }

            utterance.rate = 0.9;
            utterance.pitch = 1;
            utterance.onend = this.handleEnd;

            window.speechSynthesis.speak(utterance);
            this.isPlaying = true;
        },

        stop() {
            if (window.speechSynthesis) {
                window.speechSynthesis.cancel();
            }
            this.isPlaying = false;
        }
    }));
});
