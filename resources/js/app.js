/**
 * SIGMA — app.js
 * Extracted common inline scripts used across multiple Blade views.
 */

// ═══════════════════════════════════════════════════════════════════════════════
// 1. MOBILE NAVBAR TOGGLE
// ═══════════════════════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('mobileToggle');
    const menu = document.getElementById('mobileMenu');
    const icon = document.getElementById('mobileIcon');
    toggle?.addEventListener('click', () => {
        menu.classList.toggle('hidden');
        icon.className = menu.classList.contains('hidden') ? 'bi bi-list text-xl' : 'bi bi-x-lg text-xl';
    });
});

// ═══════════════════════════════════════════════════════════════════════════════
// 2. PROFILE DROPDOWN
// ═══════════════════════════════════════════════════════════════════════════════
(function () {
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('profileDropdownBtn');
        const dropdown = document.getElementById('profileDropdown');
        if (!btn || !dropdown) return;

        let isAnimating = false;

        window.openProfileDropdown = function () {
            if (isAnimating || !dropdown.classList.contains('hidden')) return;
            isAnimating = true;
            dropdown.classList.remove('hidden', 'is-closing');
            dropdown.classList.add('is-opening');
            btn.setAttribute('aria-expanded', 'true');
            showProfilePanel('profileMainPanel');
            dropdown.addEventListener('animationend', function handler() {
                dropdown.classList.remove('is-opening');
                isAnimating = false;
                dropdown.removeEventListener('animationend', handler);
            });
        };

        window.closeProfileDropdown = function () {
            if (isAnimating || dropdown.classList.contains('hidden')) return;
            isAnimating = true;
            dropdown.classList.remove('is-opening');
            dropdown.classList.add('is-closing');
            btn.setAttribute('aria-expanded', 'false');
            dropdown.addEventListener('animationend', function handler() {
                dropdown.classList.add('hidden');
                dropdown.classList.remove('is-closing');
                isAnimating = false;
                dropdown.removeEventListener('animationend', handler);
            });
        };

        btn.addEventListener('click', (e) => {
            e.stopPropagation();
            if (dropdown.classList.contains('hidden')) {
                openProfileDropdown();
            } else {
                closeProfileDropdown();
            }
        });

        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                closeProfileDropdown();
            }
        });

        // Close with Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeProfileDropdown();
        });
    });
})();

// ═══════════════════════════════════════════════════════════════════════════════
// 3. PROFILE PANEL NAVIGATION (Edit Profile / Change Password)
// ═══════════════════════════════════════════════════════════════════════════════
window.showProfilePanel = function (panelId, direction = null) {
    const panels = ['profileMainPanel', 'profileEditPanel', 'profilePasswordPanel'];

    panels.forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.classList.add('hidden');
        el.classList.remove('profile-panel-forward', 'profile-panel-back');
    });

    // Hide messages
    ['profileEditError', 'profileEditSuccess', 'profilePasswordError', 'profilePasswordSuccess'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.classList.add('hidden');
    });

    const target = document.getElementById(panelId);
    if (!target) return;
    target.classList.remove('hidden');

    if (direction === 'forward') target.classList.add('profile-panel-forward');
    else if (direction === 'back') target.classList.add('profile-panel-back');

    // Re-trigger staggered reveal on main panel
    if (panelId === 'profileMainPanel') {
        target.querySelectorAll('.stagger-item').forEach(item => {
            item.style.animation = 'none';
            void item.offsetWidth;
            item.style.animation = '';
        });
    }
};

// ═══════════════════════════════════════════════════════════════════════════════
// 4. TOAST NOTIFICATION (Disaster alerts)
// ═══════════════════════════════════════════════════════════════════════════════
window.showDisasterToast = function (d) {
    const container = document.getElementById('disaster-toast-container');
    const template = document.getElementById('disaster-toast-template');
    if (!container || !template) return;

    const clone = template.content.cloneNode(true);
    const toast = clone.querySelector('.toast-slide-in');

    const statusColors = {
        'AWAS': '#D32F2F',
        'SIAGA_1': '#EA580C',
        'SIAGA_2': '#7C3AED',
        'PENDING': '#FFA000',
        'RESOLVED': '#10B981',
        'DECLINE': '#64748B'
    };
    const color = statusColors[d.status] || '#FFA000';

    clone.querySelector('.toast-icon-container').style.background = `${color}15`;
    clone.querySelector('.toast-icon-container').style.color = color;

    let iconClass = 'toast-icon bi bi-bell-fill text-lg animate-pulse';
    if (d.status === 'PENDING') {
        iconClass = 'toast-icon bi bi-exclamation-circle-fill text-lg animate-pulse';
    }
    clone.querySelector('.toast-icon').className = iconClass;

    const statusEl = clone.querySelector('.toast-status');
    statusEl.style.color = color;
    statusEl.textContent = d.statusLabel || d.status;

    clone.querySelector('.toast-title').textContent = d.title;
    clone.querySelector('.toast-desc').textContent = d.description;

    const actionBtn = clone.querySelector('.toast-action');
    actionBtn.style.background = color;

    if (d.status === 'PENDING') {
        actionBtn.classList.add('hidden');
    } else {
        actionBtn.classList.remove('hidden');
        actionBtn.onclick = () => {
            if (typeof focusOnDisaster === 'function') {
                focusOnDisaster(d.id);
            } else {
                window.location.href = `/laporan/detail/${d.id}`;
            }
        };
    }

    container.appendChild(clone);

    setTimeout(() => {
        if (toast && toast.parentNode) {
            toast.classList.remove('toast-slide-in');
            toast.classList.add('toast-fade-out');
            setTimeout(() => { if (toast && toast.parentNode) toast.remove(); }, 300);
        }
    }, 10000);
};

// ═══════════════════════════════════════════════════════════════════════════════
// 5. NEWS SCROLL INDICATORS (used in user & volunteer dashboards)
// ═══════════════════════════════════════════════════════════════════════════════
window.initNewsScroll = function () {
    const newsScroll = document.querySelector('.news-scroll');
    const indicatorsContainer = document.getElementById('newsIndicators');

    if (!newsScroll || !indicatorsContainer) return;

    // Don't re-init if already has indicators
    if (indicatorsContainer.children.length > 0) return;

    for (let i = 0; i < 3; i++) {
        const dot = document.createElement('div');
        dot.className = `w-2 h-2 rounded-full transition-all duration-300 cursor-pointer ${i === 0 ? 'bg-[#2B52C3] w-8' : 'bg-slate-300'}`;
        dot.addEventListener('click', () => {
            const maxScroll = newsScroll.scrollWidth - newsScroll.clientWidth;
            newsScroll.scrollTo({ left: i === 0 ? 0 : i === 1 ? maxScroll * 0.5 : maxScroll, behavior: 'smooth' });
        });
        indicatorsContainer.appendChild(dot);
    }

    const dots = indicatorsContainer.querySelectorAll('div');

    newsScroll.addEventListener('wheel', (e) => {
        if (e.deltaY !== 0) {
            const maxScroll = newsScroll.scrollWidth - newsScroll.clientWidth;
            if (maxScroll > 0) {
                const atStart = newsScroll.scrollLeft <= 0;
                const atEnd = newsScroll.scrollLeft >= maxScroll - 1;
                if ((e.deltaY < 0 && atStart) || (e.deltaY > 0 && atEnd)) {
                    return;
                }
                e.preventDefault();
                newsScroll.scrollBy({ left: e.deltaY * 2.5, behavior: 'smooth' });
            }
        }
    }, { passive: false });

    newsScroll.addEventListener('scroll', () => {
        const maxScroll = newsScroll.scrollWidth - newsScroll.clientWidth;
        let activeIndex = 0;
        if (maxScroll > 0) {
            const pct = newsScroll.scrollLeft / maxScroll;
            if (pct > 0.33 && pct <= 0.66) activeIndex = 1;
            else if (pct > 0.66) activeIndex = 2;
        }
        dots.forEach((dot, i) => {
            dot.className = i === activeIndex ? 'w-8 h-2 rounded-full bg-[#2B52C3] transition-all duration-300' : 'w-2 h-2 rounded-full bg-slate-300 transition-all duration-300';
        });
    });
};

// Auto-init news scroll on DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    window.initNewsScroll();
});

// ═══════════════════════════════════════════════════════════════════════════════
// 6. CONFIRM MODAL HELPER (reusable across pages)
// ═══════════════════════════════════════════════════════════════════════════════
window.showConfirmModal = function (modalId, callback) {
    // Support old signature: showConfirmModal(callback) with default 'confirmModal' id
    if (typeof modalId === 'function') {
        callback = modalId;
        modalId = 'confirmModal';
    }

    const modal = document.getElementById(modalId);
    if (!modal) return;

    const backdrop = modal.querySelector('.sigma-modal-backdrop');
    const content = modal.querySelector('.sigma-modal-content');
    const okBtn = modal.querySelector('[data-action="confirm"]') || document.getElementById('confirmOkBtn');
    const cancelBtn = modal.querySelector('[data-action="cancel"]') || document.getElementById('confirmCancelBtn');

    modal.classList.remove('hidden');
    requestAnimationFrame(() => {
        if (backdrop) { backdrop.classList.add('is-visible'); }
        if (content) { content.classList.add('is-visible'); }
    });

    const closeModal = () => {
        if (backdrop) {
            backdrop.classList.remove('is-visible');
            backdrop.classList.add('is-hiding');
        }
        if (content) {
            content.classList.remove('is-visible');
            content.classList.add('is-hiding');
        }
        setTimeout(() => {
            modal.classList.add('hidden');
            if (backdrop) backdrop.classList.remove('is-hiding');
            if (content) content.classList.remove('is-hiding');
        }, 300);
    };

    // Clone buttons to remove old listeners
    if (okBtn) {
        const newOkBtn = okBtn.cloneNode(true);
        okBtn.parentNode.replaceChild(newOkBtn, okBtn);
        newOkBtn.addEventListener('click', () => { closeModal(); if (callback) callback(true); });
    }
    if (cancelBtn) {
        const newCancelBtn = cancelBtn.cloneNode(true);
        cancelBtn.parentNode.replaceChild(newCancelBtn, cancelBtn);
        newCancelBtn.addEventListener('click', () => { closeModal(); if (callback) callback(false); });
    }

    modal.onclick = (e) => {
        if (e.target === modal || e.target === backdrop) { closeModal(); if (callback) callback(false); }
    };
};

// ═══════════════════════════════════════════════════════════════════════════════
// 7. ANIMATION CLEANUP (prevent fixed modals from being clipped)
// ═══════════════════════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('main.animate-fade-up')?.addEventListener('animationend', function () {
        this.style.animation = 'none';
    });
});
