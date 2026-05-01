/**
 * validaciones.js — Validación en tiempo real de todos los campos del sistema
 * Venta-Catálogo · Versión 1.0
 *
 * Reglas aplicadas por tipo de campo:
 *  • Teléfono/Celular  → solo dígitos, auto-formato XXX-XXX-XXXX, máx 12
 *  • Cédula            → solo dígitos, auto-formato XXX-XXXXXXX-X, máx 13
 *  • RNC               → solo dígitos, auto-formato X-XX-XXXXX-X, máx 11
 *  • Email             → formato válido con @ y dominio (.com/.net/…)
 *  • Nombre/Apellidos  → solo letras, espacios y acentos
 *  • Nombre de usuario → alfanumérico + punto + guión bajo
 *  • Código de cupón   → alfanumérico + guión + guión bajo (auto-MAYÚS)
 *  • Número de tarjeta → solo dígitos, auto-espacio cada 4 dígitos
 *  • CVV               → solo dígitos, máx 4
 *  • Expiración        → dígitos, auto-slash MM/AA
 *  • Código postal     → solo dígitos, máx 5
 *  • Campos numéricos  → solo dígitos (sin letras en tiempo real)
 */

(function () {
    'use strict';

    /* ────────────────────────────────────────────────
     *  UTILIDADES
     * ──────────────────────────────────────────────── */

    function setError(input, mensaje) {
        let el = input.parentElement.querySelector('.val-error');
        if (!el) {
            el = document.createElement('span');
            el.className = 'val-error';
            el.style.cssText = 'display:block;color:#ef4444;font-size:.75rem;margin-top:3px;';
            input.parentElement.appendChild(el);
        }
        if (mensaje) {
            el.textContent = mensaje;
            el.style.display = 'block';
            input.style.borderColor = '#ef4444';
            input.style.boxShadow = '0 0 0 2px rgba(239,68,68,.15)';
        } else {
            el.textContent = '';
            el.style.display = 'none';
            input.style.borderColor = '';
            input.style.boxShadow = '';
        }
    }

    function filtrarCaracteres(input, regexPermitidos) {
        const inicio = input.selectionStart;
        const fin    = input.selectionEnd;
        const antes  = input.value;
        const nuevo  = antes.replace(regexPermitidos, '');
        if (nuevo !== antes) {
            input.value = nuevo;
            const diff = antes.length - nuevo.length;
            input.setSelectionRange(Math.max(0, inicio - diff), Math.max(0, fin - diff));
        }
    }

    function formatearConGuiones(digitos, mascara) {
        let resultado = '';
        let pos = 0;
        for (let i = 0; i < mascara.length; i++) {
            const trozo = digitos.substr(pos, mascara[i]);
            if (!trozo) break;
            resultado += (i > 0 ? '-' : '') + trozo;
            pos += mascara[i];
        }
        return resultado;
    }

    function aplicarMascaraNumerica(input, mascara) {
        const maxDigits = mascara.reduce((a, b) => a + b, 0);
        const cursorAntes = input.selectionStart;
        const valorAntes  = input.value;

        const soloDigitos = valorAntes.replace(/\D/g, '').slice(0, maxDigits);
        const formateado  = formatearConGuiones(soloDigitos, mascara);

        if (formateado !== valorAntes) {
            input.value = formateado;
            const digitosAntesDelCursor = valorAntes.slice(0, cursorAntes).replace(/\D/g, '').length;
            let contDigitos = 0;
            let nuevoCursor = 0;
            for (let i = 0; i < formateado.length; i++) {
                if (/\d/.test(formateado[i])) contDigitos++;
                if (contDigitos >= digitosAntesDelCursor) { nuevoCursor = i + 1; break; }
                nuevoCursor = i + 1;
            }
            input.setSelectionRange(nuevoCursor, nuevoCursor);
        }
    }

    /* ────────────────────────────────────────────────
     *  VALIDADORES POR TIPO
     * ──────────────────────────────────────────────── */

    function initTelefono(input) {
        input.setAttribute('maxlength', '12');
        input.setAttribute('placeholder', input.placeholder || '829-000-0000');
        input.setAttribute('inputmode', 'numeric');
        input.addEventListener('input', function () { aplicarMascaraNumerica(this, [3, 3, 4]); });
        input.addEventListener('keydown', function (e) {
            if (e.key.length === 1 && !/[\d]/.test(e.key) && !e.ctrlKey && !e.metaKey) e.preventDefault();
        });
        input.addEventListener('blur', function () {
            const d = this.value.replace(/\D/g, '');
            setError(this, this.value && d.length !== 10 ? 'Teléfono incompleto. Formato: 829-000-0000' : null);
        });
        input.addEventListener('focus', function () { setError(this, null); });
    }

    function initCedula(input) {
        input.setAttribute('maxlength', '13');
        input.setAttribute('placeholder', input.placeholder || '001-0000000-0');
        input.setAttribute('inputmode', 'numeric');
        input.addEventListener('input', function () { aplicarMascaraNumerica(this, [3, 7, 1]); });
        input.addEventListener('keydown', function (e) {
            if (e.key.length === 1 && !/[\d]/.test(e.key) && !e.ctrlKey && !e.metaKey) e.preventDefault();
        });
        input.addEventListener('blur', function () {
            const d = this.value.replace(/\D/g, '');
            setError(this, this.value && d.length !== 11 ? 'Cédula incompleta. Formato: 001-0000000-0 (11 dígitos)' : null);
        });
        input.addEventListener('focus', function () { setError(this, null); });
    }

    function initRNC(input) {
        input.setAttribute('maxlength', '12');
        input.setAttribute('placeholder', input.placeholder || '1-31-00000-0');
        input.setAttribute('inputmode', 'numeric');
        input.addEventListener('input', function () { aplicarMascaraNumerica(this, [1, 2, 5, 1]); });
        input.addEventListener('keydown', function (e) {
            if (e.key.length === 1 && !/[\d]/.test(e.key) && !e.ctrlKey && !e.metaKey) e.preventDefault();
        });
        input.addEventListener('blur', function () {
            const d = this.value.replace(/\D/g, '');
            setError(this, this.value && d.length !== 9 ? 'RNC inválido. Debe tener 9 dígitos. Ej: 1-31-00000-0' : null);
        });
        input.addEventListener('focus', function () { setError(this, null); });
    }

    function initEmail(input) {
        const regex = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/;
        input.addEventListener('input', function () {
            if (this.value.includes(' ')) this.value = this.value.replace(/\s/g, '');
        });
        input.addEventListener('blur', function () {
            if (!this.value) { setError(this, null); return; }
            setError(this, !regex.test(this.value) ? 'Correo inválido. Debe contener @ y un dominio válido (ej: nombre@gmail.com)' : null);
        });
        input.addEventListener('focus', function () { setError(this, null); });
        const form = input.closest('form');
        if (form && !form._emailValidated) {
            form._emailValidated = true;
            form.addEventListener('submit', function (e) {
                let ok = true;
                this.querySelectorAll('input[type="email"]').forEach(function (em) {
                    if (em.value && !regex.test(em.value)) {
                        setError(em, 'Correo inválido. Debe contener @ y un dominio válido (ej: nombre@gmail.com)');
                        if (ok) em.focus();
                        ok = false;
                    }
                });
                if (!ok) e.preventDefault();
            });
        }
    }

    function initNombre(input) {
        input.addEventListener('keydown', function (e) {
            if (e.key.length === 1 && !/[a-záéíóúüñÁÉÍÓÚÜÑ\s'-]/.test(e.key) && !e.ctrlKey && !e.metaKey) e.preventDefault();
        });
        input.addEventListener('input', function () { filtrarCaracteres(this, /[^a-záéíóúüñÁÉÍÓÚÜÑ\s'-]/gi); });
        input.addEventListener('blur', function () {
            setError(this, this.value.trim().length < 2 && this.value !== '' ? 'Mínimo 2 caracteres' : null);
        });
        input.addEventListener('focus', function () { setError(this, null); });
    }

    function initNombreUsuario(input) {
        input.addEventListener('keydown', function (e) {
            if (e.key.length === 1 && !/[a-zA-Z0-9._\-]/.test(e.key) && !e.ctrlKey && !e.metaKey) e.preventDefault();
        });
        input.addEventListener('input', function () { filtrarCaracteres(this, /[^a-zA-Z0-9._\-]/g); });
        input.addEventListener('blur', function () {
            setError(this, this.value.length < 3 && this.value !== '' ? 'El usuario debe tener al menos 3 caracteres' : null);
        });
        input.addEventListener('focus', function () { setError(this, null); });
    }

    function initCodigoCupon(input) {
        input.addEventListener('keydown', function (e) {
            if (e.key.length === 1 && !/[a-zA-Z0-9_\-]/.test(e.key) && !e.ctrlKey && !e.metaKey) e.preventDefault();
        });
        input.addEventListener('input', function () {
            filtrarCaracteres(this, /[^a-zA-Z0-9_\-]/g);
            const c = this.selectionStart;
            this.value = this.value.toUpperCase();
            this.setSelectionRange(c, c);
        });
        input.addEventListener('blur', function () {
            setError(this, this.value.length < 3 && this.value !== '' ? 'El código debe tener al menos 3 caracteres' : null);
        });
        input.addEventListener('focus', function () { setError(this, null); });
    }

    function initTarjeta(input) {
        input.setAttribute('maxlength', '19');
        input.setAttribute('inputmode', 'numeric');
        input.setAttribute('placeholder', input.placeholder || '0000 0000 0000 0000');
        input.addEventListener('keydown', function (e) {
            if (e.key.length === 1 && !/[\d]/.test(e.key) && !e.ctrlKey && !e.metaKey) e.preventDefault();
        });
        input.addEventListener('input', function () {
            const c = this.selectionStart;
            const digits = this.value.replace(/\D/g, '').slice(0, 16);
            const fmt = digits.replace(/(.{4})/g, '$1 ').trim();
            this.value = fmt;
            const ex = fmt.slice(0, c).split(' ').length - 1;
            this.setSelectionRange(c + ex, c + ex);
        });
        input.addEventListener('blur', function () {
            const d = this.value.replace(/\D/g, '');
            setError(this, this.value && (d.length < 13 || d.length > 16) ? 'Número de tarjeta inválido (13-16 dígitos)' : null);
        });
        input.addEventListener('focus', function () { setError(this, null); });
    }

    function initCVV(input) {
        input.setAttribute('maxlength', '4');
        input.setAttribute('inputmode', 'numeric');
        input.addEventListener('keydown', function (e) {
            if (e.key.length === 1 && !/[\d]/.test(e.key) && !e.ctrlKey && !e.metaKey) e.preventDefault();
        });
        input.addEventListener('input', function () { filtrarCaracteres(this, /\D/g); });
        input.addEventListener('blur', function () {
            setError(this, this.value && (this.value.length < 3 || this.value.length > 4) ? 'CVV debe tener 3 o 4 dígitos' : null);
        });
        input.addEventListener('focus', function () { setError(this, null); });
    }

    function initExpiracion(input) {
        input.setAttribute('maxlength', '5');
        input.setAttribute('inputmode', 'numeric');
        input.setAttribute('placeholder', 'MM/AA');
        input.addEventListener('keydown', function (e) {
            if (e.key.length === 1 && !/[\d]/.test(e.key) && !e.ctrlKey && !e.metaKey) e.preventDefault();
        });
        input.addEventListener('input', function () {
            const d = this.value.replace(/\D/g, '').slice(0, 4);
            this.value = d.length >= 3 ? d.slice(0, 2) + '/' + d.slice(2) : d.length === 2 ? d + '/' : d;
        });
        input.addEventListener('blur', function () {
            setError(this, this.value && !/^(0[1-9]|1[0-2])\/\d{2}$/.test(this.value) ? 'Formato inválido. Usa MM/AA (ej: 09/27)' : null);
        });
        input.addEventListener('focus', function () { setError(this, null); });
    }

    function initCodigoPostal(input) {
        input.setAttribute('maxlength', '5');
        input.setAttribute('inputmode', 'numeric');
        input.addEventListener('keydown', function (e) {
            if (e.key.length === 1 && !/[\d]/.test(e.key) && !e.ctrlKey && !e.metaKey) e.preventDefault();
        });
        input.addEventListener('input', function () { filtrarCaracteres(this, /\D/g); });
        input.addEventListener('blur', function () {
            setError(this, this.value && this.value.length < 4 ? 'Código postal inválido (mínimo 4 dígitos)' : null);
        });
        input.addEventListener('focus', function () { setError(this, null); });
    }

    /* ────────────────────────────────────────────────
     *  DETECCIÓN AUTOMÁTICA DE CAMPOS
     * ──────────────────────────────────────────────── */

    function aplicarValidador(input) {
        if (input.tagName !== 'INPUT' && input.tagName !== 'TEXTAREA') return;
        if (['submit','button','checkbox','radio','hidden','file'].includes(input.type)) return;
        if (input.dataset.valInit) return;
        input.dataset.valInit = '1';

        const name        = (input.name        || '').toLowerCase();
        const id          = (input.id           || '').toLowerCase();
        const placeholder = (input.placeholder  || '').toLowerCase();
        const dataVal     = (input.dataset.val  || '').toLowerCase();

        if (/telef|celular|movil|phone|fono/.test(name + id + dataVal) ||
            /\d{3}-\d{3}-\d{4}/.test(placeholder))
            return initTelefono(input);

        if (/cedula|cédula/.test(name + id + dataVal) ||
            /\d{3}-\d{7}-\d/.test(placeholder))
            return initCedula(input);

        if (/^rnc|_rnc|rnc_|negocio_rnc|rnc_empresa/.test(name + id) ||
            dataVal === 'rnc' ||
            /\d-\d{2}-\d{5}/.test(placeholder))
            return initRNC(input);

        if (input.type === 'email' || /email|correo/.test(name + id + dataVal))
            return initEmail(input);

        if (/^nombre$|^apellidos?$|nombre_empresa|razon_social|^nombre/.test(name + id + dataVal) &&
            input.type !== 'number' && !/usuario|user/.test(name + id))
            return initNombre(input);

        if (/usuario|username/.test(name + id + dataVal))
            return initNombreUsuario(input);

        if (/^codigo$/.test(name) && /cupon|coupon/.test(window.location.pathname))
            return initCodigoCupon(input);

        if (/tarjeta|card/.test(name + id + dataVal))
            return initTarjeta(input);

        if (/^cvv$|cvc/.test(name + id + dataVal))
            return initCVV(input);

        if (/expira|expiry/.test(name + id + dataVal))
            return initExpiracion(input);

        if (/postal|zip/.test(name + id + dataVal))
            return initCodigoPostal(input);

        if (input.type === 'number') {
            if (Number.isInteger(parseFloat(input.step || '1'))) {
                input.addEventListener('keydown', function (e) {
                    if (['e','E','+','-','.',','].includes(e.key) && !e.ctrlKey && !e.metaKey)
                        e.preventDefault();
                });
            }
        }
    }

    function inicializar() {
        document.querySelectorAll('input, textarea').forEach(aplicarValidador);
        const observer = new MutationObserver(function (mutations) {
            mutations.forEach(function (m) {
                m.addedNodes.forEach(function (node) {
                    if (node.nodeType !== 1) return;
                    if (node.matches('input, textarea')) aplicarValidador(node);
                    node.querySelectorAll && node.querySelectorAll('input, textarea').forEach(aplicarValidador);
                });
            });
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

    function inyectarEstilos() {
        if (document.getElementById('val-styles')) return;
        const s = document.createElement('style');
        s.id = 'val-styles';
        s.textContent = `.val-error{display:none;color:#ef4444;font-size:.75rem;margin-top:3px;line-height:1.3}`;
        document.head.appendChild(s);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { inyectarEstilos(); inicializar(); });
    } else {
        inyectarEstilos();
        inicializar();
    }

})();
