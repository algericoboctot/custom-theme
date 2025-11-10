const shortText = document.querySelectorAll(".shorttext");

if (shortText) {
    document.querySelectorAll(".shorttext").forEach(function (element) {
        var paragraphText = element.textContent.trim();
        var wordCount = paragraphText.split(/\s+/).length; // Split by whitespace
        if (wordCount >= 7) {
            element.classList.add("fixed-height");
        }
    });
}
