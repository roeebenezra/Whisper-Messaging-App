(function () {
  function ready(fn) {
    if (document.readyState !== "loading") { fn(); }
    else { document.addEventListener("DOMContentLoaded", fn); }
  }

  ready(function () {
    const input = document.querySelector("#msg");
    if (!input) return;

    // Create toggle button
    const btn = document.createElement("button");
    btn.type = "button";
    btn.setAttribute("aria-label", "Open emoji picker");
    btn.className = "emoji-btn fa-regular fa-face-smile";

    // Create popup container
    const popup = document.createElement("div");
    popup.style.display = "none";
    popup.className = "emoji-popup";

    const picker = document.createElement("emoji-picker");
    picker.setAttribute("skin-tones", "true");
    picker.setAttribute("emoji-version", "15.1");
    picker.style.width = "320px";
    picker.style.height = "380px";

    popup.appendChild(picker);
    document.body.appendChild(popup);

    // Insert button before the input
    input.parentNode.insertBefore(btn, input);

    // Position popup
    function positionUI() {
      const rect = input.getBoundingClientRect();
      popup.style.left = (window.scrollX + rect.left) + "px";
      popup.style.top = (window.scrollY + rect.top - 400) + "px";
    }
    window.addEventListener("resize", positionUI);
    window.addEventListener("scroll", positionUI, { passive: true });

    btn.addEventListener("click", function () {
      positionUI();
      popup.style.display = popup.style.display === "block" ? "none" : "block";
    });

    document.addEventListener("click", function (e) {
      if (e.target === btn || popup.contains(e.target)) return;
      popup.style.display = "none";
    });

    // Insert emoji into input
    picker.addEventListener("emoji-click", function (event) {
      const emoji = event.detail?.unicode || "";
      const start = input.selectionStart ?? input.value.length;
      const end = input.selectionEnd ?? input.value.length;
      input.value = input.value.slice(0, start) + emoji + input.value.slice(end);
      const newPos = start + emoji.length;
      input.setSelectionRange?.(newPos, newPos);
      input.focus();
      popup.style.display = "none";
    });
  });
})();
