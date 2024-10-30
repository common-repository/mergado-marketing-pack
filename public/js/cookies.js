window.mmp.cookies.functions = {
  helpers: {
    enableSection(sectionName) {
      const section = window.mmp.cookies.sections[sectionName];
      if (section && section.functions && Object.keys(section.functions).length > 0) {
        Object.values(section.functions).forEach(el => {
          el();
        });
      }
    },

    checkAndSetCategory(category, enableFunction) {
      const BreakException = {};

      try {
        // Simple
        category.names.simple.forEach(item => {
            const cookieValue = window.mmp.cookies.functions.getCookie(item);

            if (window.mmp.cookies.functions.isCookieEnabled(cookieValue)) {
              enableFunction.call(this);
              throw BreakException;
            }
        });

        // Arrays
        category.names.arrays.forEach(item => {

            // deserializeCookieYes, deserializeCookieBot
            const deserializedValue = window.mmp.cookies.functions[item.getConsentDataFunction](item);
            const cookieValue = deserializedValue[item.key];

            if (window.mmp.cookies.functions.isCookieEnabled(cookieValue)) {
              enableFunction.call(this);
              throw BreakException;
            }
        });
      } catch (e) {
        if (e !== BreakException) {
          throw e;
        }
      }
    },
  },

  enableFunctional() {
    window.mmp.cookies.functions.helpers.enableSection('functional');
  },

  enableAnalytical() {
    window.mmp.cookies.functions.helpers.enableSection('analytical');
  },

  enableAdvertisement() {
    window.mmp.cookies.functions.helpers.enableSection('advertisement');
  },

  checkAndSetFunctional() {
    window.mmp.cookies.functions.helpers.checkAndSetCategory(window.mmp.cookies.sections.functional, this.enableFunctional);
  },

  checkAndSetAnalytical() {
    window.mmp.cookies.functions.helpers.checkAndSetCategory(window.mmp.cookies.sections.analytical, this.enableAnalytical);
  },

  checkAndSetAdvertisement() {
    window.mmp.cookies.functions.helpers.checkAndSetCategory(window.mmp.cookies.sections.advertisement, this.enableAdvertisement);
  },

  checkAndSetCookies() {
    setTimeout(() => {
      this.checkAndSetFunctional();
      this.checkAndSetAnalytical();
      this.checkAndSetAdvertisement();
    }, 500);
  },

  isCookieEnabled(cookieValue) {
    const positiveOptions = ["yes", "true", true, 1, "1", "allow"];

    if (cookieValue !== "") {
      return positiveOptions.includes(cookieValue);
    } else {
      return false;
    }
  },

  getCookie(cname) {
    const name = cname + "=";
    const decodedCookie = decodeURIComponent(document.cookie);
    const ca = decodedCookie.split(";");

    for (let i = 0; i < ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) === " ") {
        c = c.substring(1);
      }
      if (c.indexOf(name) === 0) {
        return c.substring(name.length, c.length);
      }
    }

    return "";
  },
  getCookieBotConsent() {
    if (typeof window.Cookiebot !== 'undefined') {
      return window.Cookiebot.consent;
    }

    return [];
  },
  getCookieYesConsent(value) {
    const cookieValue = window.mmp.cookies.functions.getCookie(value.name);

    const parts = cookieValue.split(',');
    const cookieValuesArray = {};

    for (const part of parts) {
      const [key, value] = part.split(':');
      cookieValuesArray[key] = value;
    }

    return cookieValuesArray;
  }
};

// Check on cookies change
document.addEventListener('DOMContentLoaded', () => {
  const btns = document.querySelectorAll('#wt-cli-privacy-save-btn, #wt-cli-accept-all-btn, [data-cky-tag="accept-button"], [data-cky-tag="detail-save-button"], [data-cky-tag="detail-accept-button"]');

  btns.forEach(btn => {
    btn.addEventListener('click', () => {
      window.mmp.cookies.functions.checkAndSetCookies();
    });
  });
});

// Complianz plugin
document.addEventListener('cmplz_enable_category', consentData => {
  setTimeout(() => {
    const {category} = consentData.detail;
    if (category === 'statistics') {
      window.mmp.cookies.functions.checkAndSetAnalytical();
    } else if (category === 'marketing') {
      window.mmp.cookies.functions.checkAndSetAdvertisement();
    } else if (category === 'functional') {
      window.mmp.cookies.functions.checkAndSetFunctional();
    }
  }, 500);
});

window.addEventListener('CookiebotOnAccept', () => {
  setTimeout(() => {
    window.mmp.cookies.functions.checkAndSetAnalytical();
    window.mmp.cookies.functions.checkAndSetAdvertisement();
    window.mmp.cookies.functions.checkAndSetFunctional();
  }, 500);
});

window.addEventListener('CookiebotOnDecline', () => {
  setTimeout(() => {
    window.mmp.cookies.functions.checkAndSetAnalytical();
    window.mmp.cookies.functions.checkAndSetAdvertisement();
    window.mmp.cookies.functions.checkAndSetFunctional();
  }, 500);
});
