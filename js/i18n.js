document.addEventListener('DOMContentLoaded', () => {
    const langSwitch = document.querySelector('.lang-switch');
    let currentLang = localStorage.getItem('lang') || 'en';

    // Set initial language
    setLanguage(currentLang);

    if (langSwitch) {
        langSwitch.addEventListener('click', (e) => {
            e.preventDefault();
            currentLang = currentLang === 'en' ? 'ar' : 'en';
            setLanguage(currentLang);
            
            // Optionally dispatch event if other scripts need to know
            document.dispatchEvent(new CustomEvent('languageChanged', { detail: { lang: currentLang } }));
        });
    }

    function setLanguage(lang) {
        localStorage.setItem('lang', lang);
        
        // Update html dir and lang
        document.documentElement.setAttribute('lang', lang);
        document.documentElement.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
        
        // Update toggle text
        if (langSwitch) {
            langSwitch.textContent = lang === 'en' ? 'AR' : 'EN';
            langSwitch.title = lang === 'en' ? 'Switch to Arabic' : 'Switch to English';
        }
        
        // Translate all static texts
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            const translation = getNestedTranslation(translations[lang], key);
            if (translation) {
                if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
                    el.placeholder = translation;
                } else {
                    el.innerHTML = translation;
                }
            }
        });
    }

    function getNestedTranslation(obj, path) {
        return path.split('.').reduce((prev, curr) => prev && prev[curr], obj);
    }
});
