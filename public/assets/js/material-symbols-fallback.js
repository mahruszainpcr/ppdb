(function () {
  var iconMap = {
    account_balance: 'ri-bank-line',
    account_box: 'ri-account-box-line',
    account_circle: 'ri-account-circle-line',
    add_reaction: 'ri-emotion-happy-line',
    apartment: 'ri-community-line',
    apps: 'ri-apps-2-line',
    article: 'ri-article-line',
    auto_stories: 'ri-book-open-line',
    bookmark_manager: 'ri-bookmark-line',
    calculate: 'ri-calculator-line',
    category: 'ri-grid-line',
    chat: 'ri-chat-3-line',
    chat_error: 'ri-chat-check-line',
    clarify: 'ri-file-list-3-line',
    co_present: 'ri-slideshow-line',
    contact_page: 'ri-contacts-book-3-line',
    content_copy: 'ri-file-copy-line',
    content_paste: 'ri-clipboard-line',
    credit_card: 'ri-bank-card-line',
    dark_mode: 'ri-moon-line',
    dashboard: 'ri-dashboard-line',
    date_range: 'ri-calendar-line',
    deployed_code: 'ri-code-box-line',
    description: 'ri-file-text-line',
    diversity_3: 'ri-team-line',
    donut_large: 'ri-donut-chart-line',
    edit_document: 'ri-file-edit-line',
    error: 'ri-error-warning-line',
    event: 'ri-calendar-event-line',
    family_restroom: 'ri-parent-line',
    featured_video: 'ri-video-line',
    folder: 'ri-folder-line',
    folder_open: 'ri-folder-open-line',
    format_list_bulleted: 'ri-list-unordered',
    forum: 'ri-message-3-line',
    fullscreen: 'ri-fullscreen-line',
    gallery_thumbnail: 'ri-gallery-line',
    grid_view: 'ri-layout-grid-line',
    group: 'ri-group-line',
    group_add: 'ri-user-add-line',
    groups: 'ri-team-line',
    handshake: 'ri-hand-heart-line',
    hotel: 'ri-hotel-bed-line',
    insights: 'ri-line-chart-line',
    keyboard_arrow_down: 'ri-arrow-down-s-line',
    keyboard_arrow_left: 'ri-arrow-left-s-line',
    keyboard_arrow_right: 'ri-arrow-right-s-line',
    layers: 'ri-stack-line',
    light_mode: 'ri-sun-line',
    local_activity: 'ri-ticket-2-line',
    location_away: 'ri-map-pin-line',
    lock: 'ri-lock-line',
    lock_open: 'ri-lock-unlock-line',
    logout: 'ri-logout-box-r-line',
    lunch_dining: 'ri-restaurant-line',
    mail: 'ri-mail-line',
    manage_accounts: 'ri-user-settings-line',
    map: 'ri-map-2-line',
    mark_email_unread: 'ri-mail-unread-line',
    menu: 'ri-menu-line',
    menu_book: 'ri-book-2-line',
    note_stack: 'ri-sticky-note-line',
    notifications: 'ri-notification-3-line',
    paid: 'ri-money-dollar-circle-line',
    payments: 'ri-secure-payment-line',
    person: 'ri-user-line',
    pie_chart: 'ri-pie-chart-line',
    place: 'ri-map-pin-2-line',
    public: 'ri-global-line',
    qr_code_scanner: 'ri-qr-scan-2-line',
    real_estate_agent: 'ri-home-8-line',
    school: 'ri-school-line',
    search: 'ri-search-line',
    settings: 'ri-settings-3-line',
    share: 'ri-share-line',
    shopping_cart: 'ri-shopping-cart-2-line',
    sms: 'ri-message-2-line',
    space_dashboard: 'ri-layout-4-line',
    star: 'ri-star-line',
    stethoscope: 'ri-heart-pulse-line',
    store: 'ri-store-2-line',
    support: 'ri-customer-service-2-line',
    table: 'ri-table-line',
    table_chart: 'ri-bar-chart-box-line',
    task_alt: 'ri-checkbox-circle-line',
    team_dashboard: 'ri-dashboard-line',
    token: 'ri-coin-line',
    translate: 'ri-translate-2',
    unfold_more: 'ri-arrow-up-down-line',
    widgets: 'ri-function-line',
    work: 'ri-briefcase-4-line'
  };

  function applyFallback(root) {
    var icons = (root || document).querySelectorAll('.material-symbols-outlined');

    icons.forEach(function (icon) {
      if (icon.dataset.iconFallbackReady === 'true') {
        return;
      }

      var ligature = (icon.textContent || '').trim();
      var remixClass = iconMap[ligature];

      if (!remixClass) {
        return;
      }

      icon.classList.add('material-symbols-fallback', remixClass);
      icon.textContent = '';
      icon.setAttribute('aria-hidden', icon.getAttribute('aria-hidden') || 'true');
      icon.dataset.iconFallbackReady = 'true';
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () {
      applyFallback(document);
    });
  } else {
    applyFallback(document);
  }

  if (typeof MutationObserver !== 'undefined') {
    var observer = new MutationObserver(function (mutations) {
      mutations.forEach(function (mutation) {
        mutation.addedNodes.forEach(function (node) {
          if (node.nodeType !== 1) {
            return;
          }

          if (node.matches && node.matches('.material-symbols-outlined')) {
            applyFallback(node.parentNode || document);
            return;
          }

          if (node.querySelectorAll) {
            applyFallback(node);
          }
        });
      });
    });

    observer.observe(document.documentElement, {
      childList: true,
      subtree: true
    });
  }

  window.applyMaterialSymbolFallback = applyFallback;
})();
