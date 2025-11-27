document.addEventListener("DOMContentLoaded", function () {
    const titleInput = document.querySelector('input[name="title"]');
    const suggestionsBox = document.getElementById("title-suggestions");

    if (!titleInput || !suggestionsBox) {
        return;
    }

    let timer = null;

    titleInput.addEventListener("keyup", function () {
        const query = titleInput.value.trim();

        if (timer) {
            clearTimeout(timer);
        }

        if (query.length < 2) {
            suggestionsBox.innerHTML = "";
            suggestionsBox.style.display = "none";
            return;
        }

        timer = setTimeout(() => {
            fetch("ajax_search.php?term=" + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    suggestionsBox.innerHTML = "";

                    if (!data || data.length === 0) {
                        suggestionsBox.style.display = "none";
                        return;
                    }

                    data.forEach(title => {
                        const item = document.createElement("div");
                        item.textContent = title;
                        item.className = "suggestion-item";
                        item.addEventListener("click", () => {
                            titleInput.value = title;
                            suggestionsBox.innerHTML = "";
                            suggestionsBox.style.display = "none";
                        });
                        suggestionsBox.appendChild(item);
                    });

                    suggestionsBox.style.display = "block";
                })
                .catch(() => {
                    suggestionsBox.innerHTML = "";
                    suggestionsBox.style.display = "none";
                });
        }, 250);
    });

    document.addEventListener("click", function (e) {
        if (e.target !== titleInput && !suggestionsBox.contains(e.target)) {
            suggestionsBox.innerHTML = "";
            suggestionsBox.style.display = "none";
        }
    });
});
