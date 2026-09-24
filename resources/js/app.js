import Alpine from 'alpinejs';

window.Alpine = Alpine;

const csrf = () => document.querySelector('meta[name="csrf-token"]').content;

/**
 * Global search box with live results.
 */
Alpine.data('globalSearch', (endpoint) => ({
    term: '',
    open: false,
    loading: false,
    groups: {},
    timer: null,
    search() {
        clearTimeout(this.timer);
        if (this.term.trim().length < 2) {
            this.groups = {};
            return;
        }
        this.timer = setTimeout(async () => {
            this.loading = true;
            const response = await fetch(`${endpoint}?q=${encodeURIComponent(this.term)}`, {
                headers: { Accept: 'application/json' },
            });
            this.groups = await response.json();
            this.loading = false;
            this.open = true;
        }, 200);
    },
    get isEmpty() {
        return Object.keys(this.groups).length === 0;
    },
}));

/**
 * Checkbox selection for bulk actions on tables.
 */
Alpine.data('bulkSelect', () => ({
    selected: [],
    toggleAll(event, ids) {
        this.selected = event.target.checked ? ids.map(String) : [];
    },
}));

/**
 * Drag-and-drop deal pipeline board.
 */
Alpine.data('pipeline', () => ({
    dragging: null,
    over: null,
    start(event, id) {
        this.dragging = id;
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', id);
    },
    end() {
        this.dragging = null;
        this.over = null;
    },
    async drop(event, stage) {
        const column = event.currentTarget.querySelector('[data-column]');
        const card = document.querySelector(`[data-deal="${this.dragging}"]`);
        if (!card || !column) {
            return this.end();
        }

        const after = [...column.querySelectorAll('[data-deal]')].find((element) => {
            const box = element.getBoundingClientRect();
            return element !== card && event.clientY < box.top + box.height / 2;
        });
        after ? column.insertBefore(card, after) : column.appendChild(card);

        const id = this.dragging;
        this.end();
        this.recalculate();

        await fetch(card.dataset.moveUrl, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
            body: JSON.stringify({
                stage,
                order: [...column.querySelectorAll('[data-deal]')].map((element) => Number(element.dataset.deal)),
            }),
        });
    },
    recalculate() {
        document.querySelectorAll('[data-stage-column]').forEach((stageColumn) => {
            const cards = [...stageColumn.querySelectorAll('[data-deal]')];
            const total = cards.reduce((sum, card) => sum + Number(card.dataset.value || 0), 0);
            stageColumn.querySelector('[data-stage-total]').textContent =
                '$' + total.toLocaleString('en-US', { maximumFractionDigits: 0 });
            stageColumn.querySelector('[data-stage-count]').textContent =
                cards.length + (cards.length === 1 ? ' deal' : ' deals');
            stageColumn.querySelector('[data-empty]')?.classList.toggle('hidden', cards.length > 0);
        });
    },
}));

/**
 * Toggle an activity's done state without a full page reload.
 */
Alpine.data('doneToggle', (done, url) => ({
    done,
    async toggle() {
        this.done = !this.done;
        const response = await fetch(url, {
            method: 'PATCH',
            headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
        });
        if (response.ok) {
            this.done = (await response.json()).done;
        } else {
            this.done = !this.done;
        }
    },
}));

Alpine.start();
