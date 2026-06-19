(function () {
  const root = document.querySelector(".bb-books");
  if (!root) {
    return;
  }

  const config = window.bbBibleBooksTable || {};
  const tiles = Array.from(root.querySelectorAll("[data-book-tile]"));
  const filters = Array.from(root.querySelectorAll("[data-book-filter]"));
  const detail = root.querySelector("[data-book-detail]");
  const completeButton = root.querySelector("[data-book-complete]");
  const progressCount = root.querySelector("[data-progress-count]");
  const progressMeter = root.querySelector("[data-progress-meter]");
  const completed = new Set(Array.isArray(config.completed) ? config.completed : []);
  let currentTile = tiles[0] || null;
  let currentFilter = "all";

  const detailFields = detail
    ? {
        group: detail.querySelector("[data-book-detail-group]"),
        title: detail.querySelector("[data-book-detail-title]"),
        testament: detail.querySelector("[data-book-detail-testament]"),
        chapters: detail.querySelector("[data-book-detail-chapters]"),
        summary: detail.querySelector("[data-book-detail-summary]"),
      }
    : {};

  function chapterLabel(count) {
    const total = Number(count) || 0;
    return `${total.toLocaleString()} ${total === 1 ? "chapter" : "chapters"}`;
  }

  function setCompleteState(tile, isCompleted) {
    tile.classList.toggle("is-completed", isCompleted);
    tile.setAttribute("aria-pressed", isCompleted ? "true" : "false");
  }

  function updateProgress(count) {
    const total = tiles.length;
    const next = Number(count);
    const safeCount = Number.isFinite(next) ? next : completed.size;

    if (progressCount) {
      progressCount.textContent = safeCount.toLocaleString();
    }

    if (progressMeter) {
      progressMeter.style.width = `${total ? Math.round((safeCount / total) * 100) : 0}%`;
    }
  }

  function updateCompleteButton(tile) {
    if (!completeButton || !tile) {
      return;
    }

    const isCompleted = completed.has(tile.dataset.book);
    completeButton.classList.toggle("is-completed", isCompleted);
    completeButton.textContent = isCompleted ? "Completed" : "Mark complete";
    completeButton.disabled = false;
  }

  function selectTile(tile) {
    if (!tile) {
      return;
    }

    tiles.forEach((item) => item.classList.toggle("is-selected", item === tile));
    currentTile = tile;

    if (detailFields.group) {
      detailFields.group.textContent = tile.dataset.groupLabel || "";
    }
    if (detailFields.title) {
      detailFields.title.textContent = tile.dataset.title || "";
    }
    if (detailFields.testament) {
      detailFields.testament.textContent = tile.dataset.testament || "";
    }
    if (detailFields.chapters) {
      detailFields.chapters.textContent = chapterLabel(tile.dataset.chapters);
    }
    if (detailFields.summary) {
      detailFields.summary.textContent = tile.dataset.summary || "";
    }

    updateCompleteButton(tile);
  }

  function applyFilter(filter) {
    currentFilter = filter;

    filters.forEach((button) => {
      button.classList.toggle("is-active", button.dataset.bookFilter === filter);
    });

    tiles.forEach((tile) => {
      const show =
        filter === "all" ||
        tile.dataset.testament === filter ||
        (filter === "completed" && completed.has(tile.dataset.book));

      tile.classList.toggle("is-filtered", !show);
    });
  }

  async function toggleCurrentBook() {
    if (!currentTile || !completeButton || !config.loggedIn) {
      return;
    }

    const book = currentTile.dataset.book;
    const body = new FormData();
    body.append("action", "bb_bible_books_table_toggle");
    body.append("nonce", config.nonce || "");
    body.append("book", book);

    completeButton.disabled = true;

    try {
      const response = await fetch(config.ajaxUrl, {
        method: "POST",
        credentials: "same-origin",
        body,
      });
      const result = await response.json();

      if (!result.success) {
        throw new Error(result.data && result.data.message ? result.data.message : "Unable to save progress.");
      }

      if (result.data.completed) {
        completed.add(book);
      } else {
        completed.delete(book);
      }

      setCompleteState(currentTile, result.data.completed);
      updateProgress(result.data.count);
      updateCompleteButton(currentTile);
      applyFilter(currentFilter);
    } catch (error) {
      updateCompleteButton(currentTile);
    }
  }

  tiles.forEach((tile) => {
    setCompleteState(tile, completed.has(tile.dataset.book));
    tile.addEventListener("click", () => selectTile(tile));
  });

  filters.forEach((button) => {
    button.addEventListener("click", () => applyFilter(button.dataset.bookFilter || "all"));
  });

  if (completeButton) {
    completeButton.addEventListener("click", toggleCurrentBook);
  }

  selectTile(currentTile);
  updateProgress(completed.size);
})();
