(() => {
  'use strict';

  const grids = Array.from(document.querySelectorAll('.latest-posts-mosaic'));
  if (!grids.length) {
    return;
  }

  const mobileQuery = window.matchMedia('(max-width: 639px)');
  let scheduled = false;

  const scheduleLayout = () => {
    if (scheduled) {
      return;
    }
    scheduled = true;
    window.requestAnimationFrame(() => {
      scheduled = false;
      grids.forEach(layoutGrid);
    });
  };

  const getGridMetrics = (grid) => {
    const styles = window.getComputedStyle(grid);
    const rowGap = parseFloat(styles.rowGap) || 0;
    const rowHeight = parseFloat(styles.gridAutoRows) || 0;
    return { rowGap, rowHeight };
  };

  const resetGridItems = (grid) => {
    grid.querySelectorAll('.mosaic-item').forEach((item) => {
      item.style.removeProperty('grid-row-end');
    });
  };

  const layoutGrid = (grid) => {
    if (mobileQuery.matches) {
      resetGridItems(grid);
      return;
    }

    const { rowGap, rowHeight } = getGridMetrics(grid);
    if (!rowHeight) {
      resetGridItems(grid);
      return;
    }

    grid.querySelectorAll('.mosaic-item').forEach((item) => {
      item.style.removeProperty('grid-row-end');
      const contentHeight = item.scrollHeight;
      const span = Math.ceil((contentHeight + rowGap) / (rowHeight + rowGap));
      item.style.gridRowEnd = `span ${span}`;
    });
  };

  if ('ResizeObserver' in window) {
    const observer = new ResizeObserver(() => scheduleLayout());
    grids.forEach((grid) => {
      grid.querySelectorAll('.mosaic-item').forEach((item) => observer.observe(item));
    });
  }

  grids.forEach((grid) => {
    grid.querySelectorAll('img').forEach((img) => {
      if (!img.complete) {
        img.addEventListener('load', scheduleLayout, { once: true });
        img.addEventListener('error', scheduleLayout, { once: true });
      }
    });
  });

  window.addEventListener('resize', scheduleLayout, { passive: true });
  if (mobileQuery.addEventListener) {
    mobileQuery.addEventListener('change', scheduleLayout);
  } else if (mobileQuery.addListener) {
    mobileQuery.addListener(scheduleLayout);
  }

  scheduleLayout();
  window.addEventListener('load', scheduleLayout, { once: true });
})();
