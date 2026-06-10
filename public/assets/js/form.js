/**
 * DSGVO ADV - Form Validation and AJAX Submission
 * Client-side form validation and AJAX handling for agreement creation
 */

class AgreementForm {
  constructor() {
    this.form = document.getElementById("agreement-form");
    this.submitBtn = document.getElementById("submit-btn");
    this.loadingIndicator = document.getElementById("loading-indicator");
    this.successMessage = document.getElementById("success-message");
    this.errorMessage = document.getElementById("error-message");
    this.errorText = document.getElementById("error-text");

    this.init();
  }

  init() {
    if (!this.form) return;

    // Load CSRF token
    this.loadCSRFToken();

    // Event listeners
    this.form.addEventListener("submit", (e) => this.handleSubmit(e));
    this.form.addEventListener("input", (e) => this.handleInput(e));
    this.form.addEventListener("blur", (e) => this.handleBlur(e), true);

    // Preview button
    const previewBtn = document.getElementById("preview-btn");
    if (previewBtn) {
      previewBtn.addEventListener("click", () => this.showPreview());
    }

    // Modal close buttons
    const modalCloseBtn = document.getElementById("modal-close-btn");
    const modalCloseBtnFooter = document.getElementById(
      "modal-close-btn-footer"
    );
    if (modalCloseBtn) {
      modalCloseBtn.addEventListener("click", () => this.closePreview());
    }
    if (modalCloseBtnFooter) {
      modalCloseBtnFooter.addEventListener("click", () => this.closePreview());
    }

    // Modal submit button
    const modalSubmitBtn = document.getElementById("modal-submit-btn");
    if (modalSubmitBtn) {
      modalSubmitBtn.addEventListener("click", () => this.submitFromModal());
    }

    // Close modal on overlay click
    const modalOverlay = document.querySelector(".modal-overlay");
    if (modalOverlay) {
      modalOverlay.addEventListener("click", () => this.closePreview());
    }

    // Close modal on ESC key
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") {
        this.closePreview();
      }
    });

    // Real-time validation
    this.setupRealTimeValidation();

    // Progress indicator
    this.updateProgress(1);
  }

  /**
   * Handle form submission
   */
  async handleSubmit(event) {
    event.preventDefault();

    if (!this.validateForm()) {
      this.showError("Bitte korrigieren Sie die markierten Fehler.");
      return;
    }

    this.showLoading();

    const infoSection = document.getElementById("info-section");
    if (infoSection) infoSection.style.display = "block";

    const animationStart = Date.now();
    const ANIMATION_DURATION = 6500;
    this.animateProcessSteps();

    let response;
    try {
      response = await this.submitForm(this.getFormData());
    } catch (error) {
      console.error("Form submission error:", error);
      this.hideLoading();
      this.showError("Ein Netzwerkfehler ist aufgetreten. Bitte versuchen Sie es erneut.");
      return;
    }

    if (response.success) {
      const remaining = Math.max(0, ANIMATION_DURATION - (Date.now() - animationStart));
      setTimeout(() => {
        this.hideLoading();
        this.showSuccess(response.download_token);
        this.updateProgress(3);
      }, remaining);
    } else {
      this.hideLoading();
      this.showError(response.message || "Ein Fehler ist aufgetreten.");
    }
  }

  /**
   * Handle input events for real-time validation
   */
  handleInput(event) {
    if (event.target.tagName === "INPUT") {
      this.validateField(event.target);
    }
  }

  /**
   * Handle blur events for field validation
   */
  handleBlur(event) {
    if (event.target.tagName === "INPUT") {
      this.validateField(event.target);
    }
  }

  /**
   * Setup real-time validation
   */
  setupRealTimeValidation() {
    const inputs = this.form.querySelectorAll("input, select");
    inputs.forEach((input) => {
      if (input.tagName === "SELECT") {
        input.addEventListener("change", () => this.validateField(input));
      } else {
        input.addEventListener("input", () => this.validateField(input));
      }
      input.addEventListener("blur", () => this.validateField(input));
    });
  }

  /**
   * Validate individual field
   */
  validateField(field) {
    const fieldName = field.name;
    const value = field.value.trim();
    const errorElement = document.getElementById(`${fieldName}-error`);

    // Clear previous errors
    this.clearFieldError(fieldName);

    // Required field validation
    if (field.hasAttribute("required") && !value) {
      this.showFieldError(fieldName, "Dieses Feld ist erforderlich.");
      return false;
    }

    // Skip validation if field is empty and not required
    if (!value && !field.hasAttribute("required")) {
      return true;
    }

    // Field-specific validation
    let isValid = true;
    let errorMessage = "";

    switch (fieldName) {
      case "vorname":
      case "name":
      case "ansprechpartner":
        if (value.length < 2) {
          errorMessage = "Mindestens 2 Zeichen erforderlich.";
          isValid = false;
        } else if (value.length > 100) {
          errorMessage = "Maximal 100 Zeichen erlaubt.";
          isValid = false;
        }
        break;

      case "email":
        if (!this.isValidEmail(value)) {
          errorMessage = "Bitte geben Sie eine gültige E-Mail-Adresse ein.";
          isValid = false;
        }
        break;

      case "firma":
        if (value.length < 2) {
          errorMessage = "Mindestens 2 Zeichen erforderlich.";
          isValid = false;
        } else if (value.length > 255) {
          errorMessage = "Maximal 255 Zeichen erlaubt.";
          isValid = false;
        }
        break;

      case "anschrift":
        if (value.length < 5) {
          errorMessage = "Mindestens 5 Zeichen erforderlich.";
          isValid = false;
        } else if (value.length > 500) {
          errorMessage = "Maximal 500 Zeichen erlaubt.";
          isValid = false;
        }
        break;

      case "plz":
        if (!this.isValidPLZ(value)) {
          errorMessage =
            "Bitte geben Sie eine gültige deutsche Postleitzahl ein (5 Ziffern).";
          isValid = false;
        }
        break;

      case "ort":
        if (value.length < 2) {
          errorMessage = "Mindestens 2 Zeichen erforderlich.";
          isValid = false;
        } else if (value.length > 255) {
          errorMessage = "Maximal 255 Zeichen erlaubt.";
          isValid = false;
        }
        break;
    }

    // Show error if validation failed
    if (!isValid) {
      this.showFieldError(fieldName, errorMessage);
    }

    return isValid;
  }

  /**
   * Validate entire form
   */
  validateForm() {
    const inputs = this.form.querySelectorAll(
      "input[required], select[required]"
    );
    let isValid = true;

    inputs.forEach((input) => {
      if (!this.validateField(input)) {
        isValid = false;
      }
    });

    return isValid;
  }

  /**
   * Email validation
   */
  isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  /**
   * German postal code validation
   */
  isValidPLZ(plz) {
    const plzRegex = /^[0-9]{5}$/;
    return plzRegex.test(plz);
  }

  /**
   * Show field error
   */
  showFieldError(fieldName, message) {
    const errorElement = document.getElementById(`${fieldName}-error`);
    if (errorElement) {
      errorElement.textContent = message;
      errorElement.style.display = "block";
    }

    // Add error class to input
    const input = document.getElementById(fieldName);
    if (input) {
      input.classList.add("error");
    }
  }

  /**
   * Clear field error
   */
  clearFieldError(fieldName) {
    const errorElement = document.getElementById(`${fieldName}-error`);
    if (errorElement) {
      errorElement.textContent = "";
      errorElement.style.display = "none";
    }

    // Remove error class from input
    const input = document.getElementById(fieldName);
    if (input) {
      input.classList.remove("error");
    }
  }

  /**
   * Load CSRF token
   */
  async loadCSRFToken() {
    try {
      const response = await fetch("get_csrf_token.php", {
        method: "GET",
        headers: {
          "X-Requested-With": "XMLHttpRequest",
        },
      });

      if (response.ok) {
        const data = await response.json();
        if (data.success && data.token) {
          document.getElementById("csrf_token").value = data.token;
        }
      }
    } catch (error) {
      console.error("Failed to load CSRF token:", error);
    }
  }

  /**
   * Get form data as object
   */
  getFormData() {
    const formData = new FormData(this.form);
    const data = {};

    for (let [key, value] of formData.entries()) {
      data[key] = value.trim();
    }

    // Add IP address (will be set by server)
    data.ip_adresse = "";

    return data;
  }

  /**
   * Submit form via AJAX
   */
  async submitForm(formData) {
    const response = await fetch("process_form.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-Requested-With": "XMLHttpRequest",
      },
      body: JSON.stringify(formData),
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    return await response.json();
  }

  /**
   * Show loading state
   */
  showLoading() {
    this.form.style.display = "none";
    this.loadingIndicator.style.display = "block";
    this.hideMessages();
    this.updateProgress(2);
  }

  /**
   * Hide loading state
   */
  hideLoading() {
    this.loadingIndicator.style.display = "none";
  }

  /**
   * Show success message
   */
  showSuccess(downloadToken) {
    this.hideMessages();
    const downloadBtn = document.getElementById("download-btn");
    if (downloadBtn) {
      if (downloadToken) {
        downloadBtn.href = "download_secure.php?token=" + encodeURIComponent(downloadToken);
        downloadBtn.removeAttribute("disabled");
        downloadBtn.style.opacity = "";
        downloadBtn.style.cursor = "";
      } else {
        downloadBtn.href = "#";
        downloadBtn.setAttribute("disabled", "disabled");
        downloadBtn.style.opacity = "0.5";
        downloadBtn.style.cursor = "not-allowed";
      }
      downloadBtn.style.display = "inline-flex";
    }
    this.successMessage.style.display = "block";
  }

  /**
   * Show error message
   */
  showError(message) {
    this.errorText.textContent = message;
    this.errorMessage.style.display = "block";
    this.hideMessages();
  }

  /**
   * Hide all messages
   */
  hideMessages() {
    this.successMessage.style.display = "none";
    this.errorMessage.style.display = "none";
  }

  /**
   * Update progress indicator
   */
  updateProgress(step) {
    const steps = document.querySelectorAll(".progress-step");
    steps.forEach((stepElement, index) => {
      if (index < step) {
        stepElement.classList.add("active");
      } else {
        stepElement.classList.remove("active");
      }
    });
  }

  /**
   * Reset form
   */
  resetForm() {
    this.form.reset();
    this.form.style.display = "block";
    this.hideMessages();
    this.updateProgress(1);
    const downloadBtn = document.getElementById("download-btn");
    if (downloadBtn) {
      downloadBtn.style.display = "none";
      downloadBtn.href = "#";
    }

    // Clear all field errors
    const errorElements = this.form.querySelectorAll(".field-error");
    errorElements.forEach((element) => {
      element.textContent = "";
      element.style.display = "none";
    });

    // Remove error classes
    const inputs = this.form.querySelectorAll("input, select");
    inputs.forEach((input) => {
      input.classList.remove("error");
    });
  }

  /**
   * Show preview modal
   */
  async showPreview() {
    if (!this.validateForm()) {
      this.showError("Bitte korrigieren Sie die markierten Fehler, bevor Sie die Vorschau anzeigen können.");
      return;
    }
    // Get form data
    const formData = this.getFormData();

    // Show modal
    const modal = document.getElementById("preview-modal");
    const previewContent = document.getElementById("preview-content");

    if (!modal || !previewContent) return;

    modal.style.display = "flex";
    previewContent.innerHTML =
      '<div class="modal-loading"><div class="spinner"></div><p>Vorschau wird geladen...</p></div>';

    const musterBtn = document.getElementById("muster-download-btn");
    if (musterBtn) musterBtn.style.display = "none";
    const submitBtn2 = document.getElementById("modal-submit-btn");
    if (submitBtn2) submitBtn2.style.display = "";

    // Prevent body scroll
    document.body.style.overflow = "hidden";

    try {
      // Fetch preview
      const response = await fetch("preview_agreement.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify(formData),
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      // Get response text first to check if it's valid JSON
      const responseText = await response.text();
      let data;

      try {
        data = JSON.parse(responseText);
      } catch (parseError) {
        console.error("JSON parse error:", parseError);
        console.error("Response text:", responseText);
        throw new Error(
          "Ungültige Antwort vom Server. Bitte versuchen Sie es erneut."
        );
      }

      if (data.success && data.html) {
        previewContent.innerHTML = data.html;
      } else {
        throw new Error(data.message || "Fehler beim Laden der Vorschau");
      }
    } catch (error) {
      console.error("Preview error:", error);
      previewContent.innerHTML = `
                <div class="error-message">
                    <div class="error-icon">❌</div>
                    <h3>Fehler beim Laden der Vorschau</h3>
                    <p>${
                      error.message ||
                      "Ein unerwarteter Fehler ist aufgetreten."
                    }</p>
                </div>
            `;
    }
  }

  /**
   * Close preview modal
   */
  closePreview() {
    const modal = document.getElementById("preview-modal");
    if (modal) {
      modal.style.display = "none";
      document.body.style.overflow = "";
    }
  }

  /**
   * Submit form from modal
   */
  async submitFromModal() {
    this.closePreview();
    this.showLoading();

    const infoSection = document.getElementById("info-section");
    if (infoSection) infoSection.style.display = "block";

    const animationStart = Date.now();
    const ANIMATION_DURATION = 6500;
    this.animateProcessSteps();

    let response;
    try {
      response = await this.submitForm(this.getFormData());
    } catch (error) {
      console.error("Form submission error:", error);
      this.hideLoading();
      this.showError("Ein Netzwerkfehler ist aufgetreten. Bitte versuchen Sie es erneut.");
      return;
    }

    if (response.success) {
      const remaining = Math.max(0, ANIMATION_DURATION - (Date.now() - animationStart));
      setTimeout(() => {
        this.hideLoading();
        this.showSuccess(response.download_token);
        this.updateProgress(3);
      }, remaining);
    } else {
      this.hideLoading();
      this.showError(response.message || "Ein Fehler ist aufgetreten.");
    }
  }

  /**
   * Animate process steps with green checkmarks
   */
  animateProcessSteps() {
    const steps = document.querySelectorAll("#process-steps .info-step");

    // Reset all steps
    steps.forEach((step) => {
      step.classList.remove("completed", "processing");
    });

    // Animate step 1: Datenverarbeitung
    setTimeout(() => {
      const step1 = document.querySelector(
        '#process-steps .info-step[data-step="1"]'
      );
      if (step1) {
        step1.classList.add("processing");
      }
    }, 500);

    // Complete step 1 and start step 2: PDF-Generierung
    setTimeout(() => {
      const step1 = document.querySelector(
        '#process-steps .info-step[data-step="1"]'
      );
      const step2 = document.querySelector(
        '#process-steps .info-step[data-step="2"]'
      );
      if (step1) {
        step1.classList.remove("processing");
        step1.classList.add("completed");
      }
      if (step2) {
        step2.classList.add("processing");
      }
    }, 2000);

    // Complete step 2 and start step 3: E-Mail-Versand
    setTimeout(() => {
      const step2 = document.querySelector(
        '#process-steps .info-step[data-step="2"]'
      );
      const step3 = document.querySelector(
        '#process-steps .info-step[data-step="3"]'
      );
      if (step2) {
        step2.classList.remove("processing");
        step2.classList.add("completed");
      }
      if (step3) {
        step3.classList.add("processing");
      }
    }, 4000);

    // Complete step 3
    setTimeout(() => {
      const step3 = document.querySelector(
        '#process-steps .info-step[data-step="3"]'
      );
      if (step3) {
        step3.classList.remove("processing");
        step3.classList.add("completed");
      }
    }, 6000);
  }
}

/**
 * Global functions for form actions
 */
function resetForm() {
  if (window.agreementForm) {
    window.agreementForm.resetForm();
  }
}

function hideError() {
  if (window.agreementForm) {
    window.agreementForm.hideMessages();
  }
}

async function showMusterPreview() {
  const musterData = {
    vorname: "Max",
    name: "Muster",
    email: "max.muster@muster-gmbh.de",
    firma: "Muster GmbH",
    ansprechpartner: "Max Muster",
    anschrift: "Musterstraße 1",
    plz: "12345",
    ort: "Musterstadt",
    dienstleistung: "webhosting",
    csrf_token: document.getElementById("csrf_token")?.value || "",
  };

  const modal = document.getElementById("preview-modal");
  const content = document.getElementById("preview-content");
  const submitBtn = document.getElementById("modal-submit-btn");
  if (!modal || !content) return;

  // Modal öffnen, Submit-Button ausblenden, Muster-Download einblenden
  modal.style.display = "flex";
  if (submitBtn) submitBtn.style.display = "none";
  const musterDownloadBtn = document.getElementById("muster-download-btn");
  if (musterDownloadBtn) musterDownloadBtn.style.display = "inline-flex";
  content.innerHTML = '<div class="modal-loading"><div class="spinner"></div><p>Muster wird geladen...</p></div>';

  try {
    const response = await fetch("preview_agreement.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(musterData),
    });
    const data = await response.json();
    if (data.success && data.html) {
      content.innerHTML = data.html;
    } else {
      content.innerHTML = "<p>Vorschau konnte nicht geladen werden.</p>";
    }
  } catch (e) {
    content.innerHTML = "<p>Fehler beim Laden der Vorschau.</p>";
  }
}

/**
 * Initialize form when DOM is loaded
 */
document.addEventListener("DOMContentLoaded", function () {
  window.agreementForm = new AgreementForm();
});

/**
 * Utility functions
 */
const FormUtils = {
  /**
   * Sanitize input to prevent XSS
   */
  sanitizeInput(input) {
    return input.replace(/[<>\"'&]/g, function (match) {
      const escapeMap = {
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#x27;",
        "&": "&amp;",
      };
      return escapeMap[match];
    });
  },

  /**
   * Format form data for display
   */
  formatFormData(data) {
    return {
      name: `${data.vorname} ${data.name}`,
      company: data.firma,
      email: data.email,
      address: `${data.anschrift}, ${data.plz} ${data.ort}`,
      contact: data.ansprechpartner,
    };
  },

  /**
   * Validate German postal code
   */
  validateGermanPLZ(plz) {
    // German postal codes are 5 digits
    return /^[0-9]{5}$/.test(plz);
  },

  /**
   * Validate German phone number (optional)
   */
  validateGermanPhone(phone) {
    // Basic German phone number validation
    const phoneRegex = /^(\+49|0)[1-9]\d{1,4}\d{1,4}$/;
    return phoneRegex.test(phone.replace(/\s/g, ""));
  },
};

/**
 * Error handling
 */
window.addEventListener("error", function (event) {
  console.error("JavaScript error:", event.error);

  // Show user-friendly error message
  if (window.agreementForm) {
    window.agreementForm.showError(
      "Ein unerwarteter Fehler ist aufgetreten. Bitte laden Sie die Seite neu."
    );
  }
});

/**
 * Network error handling
 */
window.addEventListener("online", function () {
  console.log("Network connection restored");
});

window.addEventListener("offline", function () {
  if (window.agreementForm) {
    window.agreementForm.showError(
      "Keine Internetverbindung. Bitte überprüfen Sie Ihre Verbindung."
    );
  }
});
