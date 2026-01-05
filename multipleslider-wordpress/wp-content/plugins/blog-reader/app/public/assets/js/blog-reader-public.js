jQuery(document).ready(function ($) {
	// SpeechSynthesis init.
	const speechSynthesis = window.speechSynthesis;

	// Stop speechSynthesis on Page load - For chrome.
	speechSynthesis.cancel();

	let isPlaying = false;
	let isPaused = false;
	const icon = $(".gg-play-button-o");
	const speechUtterance = new SpeechSynthesisUtterance();

	// Event handler for when speech ends
	speechUtterance.onend = function () {
		isPlaying = false;
		isPaused = false;
		icon.removeClass().addClass("gg-play-button-o");
	};

	// Click event for the "Play" button
	$(document).on("click", "#br-tts-play", function () {
		if (isPlaying) {
			if (isPaused) {
				// If paused, resume.
				speechSynthesis.resume();
				isPaused = false;
				icon.removeClass().addClass("gg-play-pause-o");
			} else {
				// If playing, pause
				speechSynthesis.pause();
				isPaused = true;
				icon.removeClass().addClass("gg-play-button-o");
			}
		} else {
			// If not playing, start speaking
			const content = $(".br-post-content").text();
			speechUtterance.text = content;
			speechSynthesis.speak(speechUtterance);
			isPlaying = true;
			isPaused = false;
			icon.removeClass().addClass("gg-play-pause-o");
		}
	});

	// Click event for the "Stop" button
	$(document).on("click", "#br-tts-stop", function () {
		speechSynthesis.cancel();
		isPlaying = false;
		isPaused = false;
		icon.removeClass().addClass("gg-play-button-o");
	});
});
