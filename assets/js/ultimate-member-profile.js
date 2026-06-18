(function () {
  function getTabKey(item) {
    var link = item.querySelector('a');
    var title = item.querySelector('.title');
    var slugClass = Array.prototype.find.call(item.classList, function (className) {
      return className.indexOf('um-profile-nav-') === 0 && className !== 'um-profile-nav-item';
    });
    var label = title && title.textContent.trim()
      ? title.textContent.trim().toLowerCase()
      : '';

    if (!label && link) {
      label = link.textContent.trim().toLowerCase();
    }

    if (label) {
      return 'label:' + label;
    }

    if (link && link.dataset && link.dataset.tab) {
      return 'tab:' + link.dataset.tab.toLowerCase();
    }

    if (slugClass) {
      return 'slug:' + slugClass.toLowerCase();
    }

    return '';
  }

  function dedupeProfileTabs(root) {
    var seen = Object.create(null);
    var items = root.querySelectorAll('.um-profile-nav .um-profile-nav-item');

    Array.prototype.forEach.call(items, function (item) {
      var key = getTabKey(item);

      if (!key) {
        return;
      }

      if (seen[key]) {
        if (item.classList.contains('active')) {
          seen[key].classList.add('active');
        }

        item.parentNode.removeChild(item);
        return;
      }

      seen[key] = item;
    });
  }

  function mountCoverInHeader(root) {
    var form = root.querySelector('.um-form');
    var cover = root.querySelector('.um-form > .um-cover');
    var header = root.querySelector('.um-header');

    if (!form || !cover || !header) {
      return;
    }

    header.insertBefore(cover, header.firstChild);
    cover.classList.add('bb-um-cover-mounted');
  }

  function addCoverUploadAction(root) {
    var body = document.body;
    var header = root.querySelector('.um-header');
    var existingAction = root.querySelector('.bb-um-cover-action');
    var uploadButton = root.querySelector('.um-field-cover_photo .um-btn-auto-width');
    var editLink = root.querySelector('.um-profile-edit a.real_url[href*="um_action=edit"]');
    var profileLink = root.querySelector('.um-name a, .um-profile-photo-img');
    var editUrl = editLink ? editLink.href : '';
    var separator;
    var action;

    if (!body.classList.contains('um-own-profile') || !header || existingAction) {
      return;
    }

    if (!editUrl && profileLink) {
      separator = profileLink.href.indexOf('?') === -1 ? '?' : '&';
      editUrl = profileLink.href + separator + 'um_action=edit';
    }

    if (!uploadButton && !editUrl) {
      return;
    }

    action = document.createElement('a');
    action.className = 'bb-um-cover-action';
    action.href = editUrl || '#';
    action.textContent = root.classList.contains('um-editing') ? 'Change Cover' : 'Edit Cover';
    action.addEventListener('click', function (event) {
      if (!uploadButton) {
        return;
      }

      event.preventDefault();
      uploadButton.click();
    });
    header.appendChild(action);
  }

  function init() {
    var profiles = document.querySelectorAll('.um.um-profile');

    Array.prototype.forEach.call(profiles, function (profile) {
      mountCoverInHeader(profile);
      addCoverUploadAction(profile);
      dedupeProfileTabs(profile);
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
