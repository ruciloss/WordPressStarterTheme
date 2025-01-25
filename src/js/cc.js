/**
 * Cookies settings
 */
CookieConsent.run({
    cookie: {
        name: "_" + window.location.hostname,
    },

    guiOptions: {
        consentModal: {
            layout: "cloud", // box,cloud,bar
            position: "bottom center", // bottom,middle,top + left,right,center
            equalWeightButtons: false,
            flipButtons: false,
        },
        preferencesModal: {
            layout: "box", // box,bar
            position: "left", // right,left (available only if bar layout selected)
            equalWeightButtons: false,
            flipButtons: true,
        },
    },

    onFirstConsent: function () {},

    onConsent: function () {},

    onChange: function () {
        if (!cc.acceptedCategory("analytics")) {
            typeof gtag === "function" &&
                gtag("consent", "update", {
                    analytics_storage: "denied",
                });
        }
    },

    categories: {
        necessary: {
            readOnly: true,
            enabled: true,
        },
        analytics: {
            autoClear: {
                cookies: [
                    {
                        name: /^(_ga|_gid)/,
                    },
                ],
            },
            services: {
                ga: {
                    label: `
                    <a
                        href="https://analytics.google.com"
                        rel="noreferrer"
                        target="_blank">
                        Google Analytics 4
                    </a>
                `,
                },
            },
        },
        ads: {
            services: {
                ga: {
                    label: `
                    <a
                        href="https://ads.google.com"
                        rel="noreferrer"
                        target="_blank">
                        Google AdSense
                    </a>
                `,
                },
                fb: {
                    label: `
                    <a
                        href="https://www.facebook.com/business/tools/meta-pixel"
                        rel="noreferrer"
                        target="_blank">
                        Facebook Pixel
                    </a>
                `,
                },
                tiktok: {
                    label: `
                    <a
                        href="https://ads.tiktok.com"
                        rel="noreferrer"
                        target="_blank">
                        TikTok Pixel
                    </a>
                `,
                },
                sklik: {
                    label: `
                    <a
                        href="https://sklik.cz"
                        rel="noreferrer"
                        target="_blank">
                        Sklik
                    </a>
                `,
                },
            },
        },
    },

    language: {
        default: "en",
        //autoDetect: "browser",
        translations: {
            en: {
                consentModal: {
                    title: "🍪 Cookie!",
                    description: "Our website uses tracking cookies to understand how you interact with it. The tracking will be enabled only if you accept explicitly.",
                    acceptAllBtn: "Accept all",
                    //acceptNecessaryBtn: 'Reject all',
                    showPreferencesBtn: "Manage preferences",
                    closeIconLabel: "Close",
                    /*footer: `
                        <a href="#link">Privacy Policy</a>
                    `*/
                },
                preferencesModal: {
                    title: "Cookie preferences <small>" + window.location.hostname + "</small>",
                    acceptAllBtn: "Accept all",
                    acceptNecessaryBtn: "Reject all",
                    savePreferencesBtn: "Save preferences",
                    closeIconLabel: "Close",
                    sections: [
                        {
                            description: 'I use cookies to ensure the basic functionalities of the website and to enhance your online experience. You can choose for each category to opt-in/out whenever you want. For more details relative to cookies and other sensitive data, please read the full <a href="' + wpde.home_url + '/ochrana-osobnich-udaju" class="cc__link">privacy policy</a>.',
                        },
                        {
                            title: 'Strictly necessary cookies <span class="pm__badge">Always enabled</span>',
                            description: "These cookies are necessary for the proper functioning of the website and are also the only ones allowed.",
                            linkedCategory: "necessary",
                            cookieTable: {
                                headers: {
                                    name: "Name",
                                    domain: "Service",
                                    description: "Description",
                                    expiration: "Expiration",
                                },
                                body: [
                                    {
                                        name: "_" + window.location.hostname,
                                        domain: "Cookie Consent",
                                        description: "Uchovává informace o udělení souhlasu pro kategorie souborů cookie",
                                        expiration: "182 dní",
                                    },
                                ],
                            },
                        },
                        {
                            title: 'Performance and Analytics cookies <span class="pm__badge">2 Services</span>',
                            description: "These cookies collect information about how you use the website, which pages you visited and which links you clicked on. All of the data is anonymized and cannot be used to identify you.",
                            linkedCategory: "analytics",
                            cookieTable: {
                                headers: {
                                    name: "Name",
                                    domain: "Service",
                                    description: "Description",
                                    expiration: "Expiration",
                                },
                                body: [
                                    {
                                        name: "_ga",
                                        domain: "Google Analytics",
                                        description: 'Cookie set by <a href="https://analytics.google.com">Google Analytics</a>.',
                                        expiration: "Expires after 12 days",
                                    },
                                    {
                                        name: "_gid",
                                        domain: "Google Analytics",
                                        description: 'Cookie set by <a href="https://analytics.google.com">Google Analytics</a>',
                                        expiration: "Session",
                                    },
                                ],
                            },
                        },
                        {
                            title: 'Advertisement and Targeting cookies <span class="pm__badge">4 Services</span>',
                            description: "Targeting and advertising cookies are specifically designed to gather information from you on your device to display advertisements to you based on relevant topics that interest you.",
                            linkedCategory: "ads",
                            cookieTable: {
                                headers: {
                                    name: "Název",
                                    domain: "Služba",
                                    description: "Popis",
                                    expiration: "Expirace",
                                },
                                body: [
                                    {
                                        name: "__gsas",
                                        domain: "Google AdSense",
                                        description: 'Cookie set by <a href="https://analytics.google.com">Google AdSense</a>',
                                        expiration: "3 months",
                                    },
                                    {
                                        name: "_fbp",
                                        domain: "Facebook Pixel",
                                        description: 'Cookie set by <a href="https://www.facebook.com/business/tools/meta-pixel">Facebook Pixel</a>',
                                        expiration: "3 months",
                                    },
                                    {
                                        name: "_ttp",
                                        domain: "TikTok Pixel",
                                        description: 'Cookie set by <a href="https://ads.tiktok.com/">TikTok Pixel</a>',
                                        expiration: "13 months",
                                    },
                                    {
                                        name: "_sid",
                                        domain: "Sklik",
                                        description: 'Cookie set by <a href="https://www.sklik.cz/">Sklik</a>',
                                        expiration: "1 month",
                                    },
                                ],
                            },
                        },
                        {
                            title: "Consent details",
                            description: `
                                <p><strong>Consent ID:</strong> <span id="consent-id">-</span></p>
                                <p><strong>Consent date:</strong> <span id="consent-timestamp">-</span></p>
                                <p><strong>Last update:</strong> <span id="last-consent-timestamp">-</span></p>
                            `,
                        },
                        {
                            title: "More information",
                            description: "For any queries in relation to my policy on cookies and your choices, please write an email to the address indicated in the privacy policy.",
                        },
                    ],
                },
            },
            cs: {
                consentModal: {
                    title: "🍪 Cookie!",
                    description: "Tento web používá soubory cookie, aby pochopil, jak s nim komunikujete. Měření bude povoleno pouze v případě, že budete souhlasit.",
                    acceptAllBtn: "Přijmout vše",
                    //acceptNecessaryBtn: 'Odmítnout vše',
                    showPreferencesBtn: "Spravovat předvolby",
                    closeIconLabel: "Close",
                    /*footer: `
                        <a href="#link">Privacy Policy</a>
                    `*/
                },
                preferencesModal: {
                    title: "Předvolby souborů cookie <small>" + window.location.hostname + "</small>",
                    acceptAllBtn: "Přijmout vše",
                    acceptNecessaryBtn: "Odmítnout vše",
                    savePreferencesBtn: "Uložit předvolby",
                    closeIconLabel: "Zavřít",
                    sections: [
                        {
                            description: 'Soubory cookie slouží k zajištění základních funkcí webu a ke zlepšení vašeho online zážitku. Pro každou kategorii si můžete vybrat, zda se chcete přihlásit/odhlásit, kdykoli budete chtít. Další podrobnosti týkající se souborů cookie a dalších citlivých údajů naleznete v úplných <a href="' + wpde.home_url + '/ochrana-osobnich-udaju" class="cc__link">zásadách ochrany osobních údajů</a>.',
                        },
                        {
                            title: 'Nezbytně nutné cookies <span class="pm__badge">Vždy povoleno</span>',
                            description: "Tyto soubory cookies jsou nezbytné pro správné fungování webových stránek a jsou také jako jediné povolené.",
                            linkedCategory: "necessary",
                            cookieTable: {
                                headers: {
                                    name: "Název",
                                    domain: "Služba",
                                    description: "Popis",
                                    expiration: "Expirace",
                                },
                                body: [
                                    {
                                        name: "_" + window.location.hostname,
                                        domain: "Souhlas s cookies",
                                        description: "Informace o udělení souhlasu pro soubory cookie",
                                        expiration: "182 dní",
                                    },
                                ],
                            },
                        },
                        {
                            title: 'Výkonnostní a Analytické cookies <span class="pm__badge">2 Služby</span>',
                            description: "Tyto soubory cookie shromažďují informace o tom, jak web používáte, které stránky jste navštívili a na které odkazy jste klikli. Všechna data jsou anonymizována a nelze je použít k vaší identifikaci.",
                            linkedCategory: "analytics",
                            cookieTable: {
                                headers: {
                                    name: "Název",
                                    domain: "Služba",
                                    description: "Popis",
                                    expiration: "Expirace",
                                },
                                body: [
                                    {
                                        name: "_ga",
                                        domain: "Google Analytics",
                                        description: 'Soubor cookie nastaven službou <a href="https://analytics.google.com">Google Analytics</a>',
                                        expiration: "12 dní",
                                    },
                                    {
                                        name: "_gid",
                                        domain: "Google Analytics",
                                        description: 'Soubor cookie nastaven službou <a href="https://analytics.google.com">Google Analytics</a>',
                                        expiration: "Relace",
                                    },
                                    {
                                        name: "_CLID",
                                        domain: "Microsoft Clarity",
                                        description: 'Soubor cookie nastaven službou <a href="https://clarity.microsoft.com">Microsoft Clarity</a>',
                                        expiration: "1 den",
                                    },
                                ],
                            },
                        },
                        {
                            title: 'Reklamní a Cílené cookies <span class="pm__badge">4 Služby</span>',
                            description: "Soubory cookie pro cílení a reklamy jsou speciálně navrženy tak, aby shromažďovaly informace od vás na vašem zařízení, aby vám mohly zobrazovat reklamy na základě relevantních témat, která vás zajímají.",
                            linkedCategory: "ads",
                            cookieTable: {
                                headers: {
                                    name: "Název",
                                    domain: "Služba",
                                    description: "Popis",
                                    expiration: "Expirace",
                                },
                                body: [
                                    {
                                        name: "__gsas",
                                        domain: "Google AdSense",
                                        description: 'Soubor cookie nastaven službou <a href="https://analytics.google.com">Google AdSense</a>',
                                        expiration: "3 měsíce",
                                    },
                                    {
                                        name: "_fbp",
                                        domain: "Facebook Pixel",
                                        description: 'Soubor cookie nastaven službou <a href="https://www.facebook.com/business/tools/meta-pixel">Facebook Pixel</a>',
                                        expiration: "3 měsíce",
                                    },
                                    {
                                        name: "_ttp",
                                        domain: "TikTok Pixel",
                                        description: 'Soubor cookie nastaven službou <a href="https://ads.tiktok.com/">TikTok Pixel</a>',
                                        expiration: "13 měsíců",
                                    },
                                    {
                                        name: "_sid",
                                        domain: "Sklik",
                                        description: 'Soubor cookie nastaven službou <a href="https://www.sklik.cz/">Sklik</a>',
                                        expiration: "1 měsíc",
                                    },
                                ],
                            },
                        },
                        {
                            title: "Podrobnosti o souhlasu",
                            description: `
                                <p><strong>ID souhlasu:</strong> <span id="consent-id">-</span></p>
                                <p><strong>Datum souhlasu:</strong> <span id="consent-timestamp">-</span></p>
                                <p><strong>Poslední aktualizace:</strong> <span id="last-consent-timestamp">-</span></p>
                            `,
                        },
                        {
                            title: "Více informací",
                            description: "Máte-li jakékoli dotazy týkající se zásad souborů cookie a vašich voleb napište prosím email na uvedenou adresu v zásadách o ochraně osobních údajů.",
                        },
                    ],
                },
            },
        },
    },
});

/**
 * @param {HTMLElement} modal
 */
const updateConsentDetails = (modal) => {
    const { consentId, consentTimestamp, lastConsentTimestamp } = CookieConsent.getCookie();

    const id = modal.querySelector("#consent-id");
    const timestamp = modal.querySelector("#consent-timestamp");
    const lastTimestamp = modal.querySelector("#last-consent-timestamp");

    id && (id.innerText = consentId);
    timestamp && (timestamp.innerText = new Date(consentTimestamp).toLocaleString());
    lastTimestamp && (lastTimestamp.innerText = new Date(lastConsentTimestamp).toLocaleString());
};

addEventListener("cc:onModalReady", ({ detail }) => {
    const { modalName, modal } = detail;

    if (!modalName === "preferencesModal") return;

    if (CookieConsent.validConsent()) {
        updateConsentDetails(modal);
        addEventListener("cc:onChange", () => updateConsentDetails(modal));
    } else {
        addEventListener("cc:onConsent", () => updateConsentDetails(modal));
    }
});
