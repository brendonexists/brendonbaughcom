(function () {
  const buttons = document.querySelectorAll("[data-prayer-button]");

  buttons.forEach((button) => {
    button.addEventListener("click", async () => {
      const postId = button.getAttribute("data-post-id");
      const count = document.querySelector(`[data-prayer-count="${postId}"]`);
      const originalText = button.textContent;
      const body = new FormData();

      body.append("action", "bb_prayer_wall_prayed");
      body.append("nonce", window.bbPrayerWall ? window.bbPrayerWall.nonce : "");
      body.append("post_id", postId);

      button.disabled = true;
      button.textContent = "Praying";

      try {
        const response = await fetch(window.bbPrayerWall.ajaxUrl, {
          method: "POST",
          credentials: "same-origin",
          body,
        });
        const result = await response.json();

        if (!result.success) {
          throw new Error(result.data && result.data.message ? result.data.message : "Unable to save prayer.");
        }

        const nextCount = Number(result.data.count) || 0;
        if (count) {
          count.textContent = `${nextCount.toLocaleString()} ${nextCount === 1 ? "prayer" : "prayers"}`;
        }

        button.textContent = "Prayed";
      } catch (error) {
        button.disabled = false;
        button.textContent = originalText;
      }
    });
  });
})();
