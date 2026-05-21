/**
 * Live password-policy indicator for the TYPO3 form framework's
 * Password / AdvancedPassword elements (form_extended overrides).
 *
 * Markup contract:
 *   <input type="password" id="..." />
 *   <div class="fe-password-policy" data-fe-password-policy data-target="..."></div>
 *
 * The script fetches the active frontend password policy once per
 * page from /_form_extended/password-policy, then renders one
 * <li> per rule. Each <li> toggles between `is-met` and `is-unmet`
 * as the user types — same regexes the TYPO3 CorePasswordValidator
 * uses on the server, so what the indicator shows green is also
 * what the form will accept.
 *
 * Multiple password fields on one page each get their own container;
 * a single fetch is shared via a module-scoped promise.
 */
(function () {
    'use strict';

    const ENDPOINT = '/_form_extended/password-policy';
    let policyPromise = null;

    function loadPolicy() {
        if (policyPromise === null) {
            policyPromise = fetch(ENDPOINT, { credentials: 'same-origin' })
                .then(function (r) { return r.ok ? r.json() : { rules: [] }; })
                .catch(function () { return { rules: [] }; });
        }
        return policyPromise;
    }

    /**
     * Same evaluation logic as TYPO3 core's CorePasswordValidator
     * (sysext/core/Classes/PasswordPolicy/Validator/CorePasswordValidator.php).
     * Keep in sync with that class — both regexes and the "special"
     * char class.
     */
    const RULES = {
        minimumLength: function (pw, value) { return pw.length >= Number(value || 0); },
        upperCaseCharacterRequired: function (pw) { return /[A-Z]/.test(pw); },
        lowerCaseCharacterRequired: function (pw) { return /[a-z]/.test(pw); },
        digitCharacterRequired: function (pw) { return /[0-9]/.test(pw); },
        // Matches the same Unicode class TYPO3 core uses.
        specialCharacterRequired: function (pw) { return /[\p{P}\p{Sm}\p{Sc}\p{Sk}\p{So}]/u.test(pw); },
    };

    function init(container) {
        const targetId = container.dataset.target;
        if (!targetId) return;
        const input = document.getElementById(targetId);
        if (!(input instanceof HTMLInputElement)) return;

        loadPolicy().then(function (policy) {
            const rules = (policy.rules || []).filter(function (r) {
                return typeof RULES[r.id] === 'function';
            });
            if (rules.length === 0) {
                // No active rules → no indicator. Empty container = no
                // visual noise on policies that disable client-side checks.
                container.hidden = true;
                return;
            }
            renderList(container, rules);
            const items = container.querySelectorAll('[data-rule-id]');
            const update = function () {
                const pw = input.value || '';
                items.forEach(function (li) {
                    const ruleId = li.dataset.ruleId;
                    const value = li.dataset.ruleValue;
                    const met = RULES[ruleId](pw, value);
                    li.classList.toggle('is-met', met);
                    li.classList.toggle('is-unmet', !met);
                });
            };
            input.addEventListener('input', update);
            // Set the initial empty-input state on attach.
            update();
        });
    }

    function renderList(container, rules) {
        // Replace whatever placeholder was there with a fresh list. This
        // is the only DOM write we do; updates after that are class
        // toggles on the existing nodes.
        const heading = container.querySelector('[data-heading]');
        const ul = document.createElement('ul');
        ul.className = 'fe-password-policy__rules';
        rules.forEach(function (rule) {
            const li = document.createElement('li');
            li.className = 'fe-password-policy__rule is-unmet';
            li.dataset.ruleId = rule.id;
            if (rule.value !== undefined && rule.value !== null) {
                li.dataset.ruleValue = String(rule.value);
            }
            const marker = document.createElement('span');
            marker.className = 'fe-password-policy__marker';
            marker.setAttribute('aria-hidden', 'true');
            const label = document.createElement('span');
            label.className = 'fe-password-policy__label';
            label.textContent = rule.label;
            li.appendChild(marker);
            li.appendChild(label);
            ul.appendChild(li);
        });
        // Wipe everything except the heading element if present, then
        // append the freshly built list.
        Array.from(container.children).forEach(function (c) {
            if (c !== heading) container.removeChild(c);
        });
        container.appendChild(ul);
    }

    function start() {
        document.querySelectorAll('[data-fe-password-policy]').forEach(init);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
