/**
 * Manual Exam Creator
 * Xử lý chọn câu hỏi, drag & drop, reorder questions
 */

(function () {
  "use strict";

  let selectedQuestions = [];
  let draggedElement = null;

  // Initialize
  function init() {
    initCheckboxes();
    initDragDrop();
    initActionButtons();
    updateStats();
  }

  // ===== CHECKBOX SELECTION =====
  function initCheckboxes() {
    const checkboxes = document.querySelectorAll(".q-checkbox");

    checkboxes.forEach((checkbox) => {
      // Load initial state
      if (checkbox.checked) {
        const questionData = getQuestionData(checkbox);
        addQuestionToPaper(questionData);
      }

      // Handle change event
      checkbox.addEventListener("change", function () {
        const questionData = getQuestionData(this);

        if (this.checked) {
          addQuestionToPaper(questionData);
          window.adminUI.showToast("Đã thêm câu hỏi", "success", 2000);
        } else {
          removeQuestionFromPaper(questionData.id);
          window.adminUI.showToast("Đã xóa câu hỏi", "info", 2000);
        }

        updateStats();
      });
    });
  }

  // Get question data from checkbox
  function getQuestionData(checkbox) {
    const item = checkbox.closest(".q-select-item");
    const label = item.querySelector(".q-select-label");
    const content = label.querySelector(".q-content p");
    const badge = label.querySelector(".badge");

    return {
      id: checkbox.id,
      text: content.textContent.trim(),
      difficulty: badge.textContent.trim(),
      difficultyClass: badge.className.split(" ")[1], // badge-easy, badge-medium, etc.
    };
  }

  // ===== ADD/REMOVE QUESTIONS =====
  function addQuestionToPaper(questionData) {
    // Check if already exists
    if (selectedQuestions.find((q) => q.id === questionData.id)) {
      return;
    }

    selectedQuestions.push(questionData);
    renderPaper();
  }

  function removeQuestionFromPaper(questionId) {
    selectedQuestions = selectedQuestions.filter((q) => q.id !== questionId);

    // Uncheck checkbox
    const checkbox = document.getElementById(questionId);
    if (checkbox) {
      checkbox.checked = false;
    }

    renderPaper();
  }

  // ===== RENDER PAPER =====
  function renderPaper() {
    const container = document.querySelector(".paper-questions");
    const emptyState = document.querySelector(".paper-empty-state");

    if (selectedQuestions.length === 0) {
      container.innerHTML = "";
      if (emptyState) {
        emptyState.style.display = "block";
      }
      return;
    }

    if (emptyState) {
      emptyState.style.display = "none";
    }

    container.innerHTML = selectedQuestions
      .map(
        (q, index) => `
      <div class="paper-question-item" draggable="true" data-id="${q.id}">
        <div class="paper-q-number">${index + 1}</div>
        <div class="paper-q-content">
          <p class="paper-q-text">
            <strong>${q.text}</strong>
          </p>
          <ul class="paper-answers">
            <li>A. Đáp án mẫu 1</li>
            <li>B. Đáp án mẫu 2</li>
            <li>C. Đáp án mẫu 3</li>
            <li>D. Đáp án mẫu 4</li>
          </ul>
        </div>
        <div class="paper-q-actions">
          <button class="q-action-btn q-move-up" title="Di chuyển lên" ${index === 0 ? "disabled" : ""}>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m18 15-6-6-6 6" />
            </svg>
          </button>
          <button class="q-action-btn q-move-down" title="Di chuyển xuống" ${index === selectedQuestions.length - 1 ? "disabled" : ""}>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="m6 9 6 6 6-6" />
            </svg>
          </button>
          <button class="q-action-btn q-remove" title="Xóa" data-id="${q.id}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M18 6 6 18M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    `,
      )
      .join("");

    // Re-init drag and drop
    initDragDrop();
    initActionButtons();
    updateStats();
  }

  // ===== DRAG AND DROP =====
  function initDragDrop() {
    const items = document.querySelectorAll(
      '.paper-question-item[draggable="true"]',
    );

    items.forEach((item) => {
      item.addEventListener("dragstart", handleDragStart);
      item.addEventListener("dragover", handleDragOver);
      item.addEventListener("drop", handleDrop);
      item.addEventListener("dragend", handleDragEnd);
    });
  }

  function handleDragStart(e) {
    draggedElement = this;
    this.classList.add("dragging");
    e.dataTransfer.effectAllowed = "move";
    e.dataTransfer.setData("text/html", this.innerHTML);
  }

  function handleDragOver(e) {
    if (e.preventDefault) {
      e.preventDefault();
    }
    e.dataTransfer.dropEffect = "move";

    const afterElement = getDragAfterElement(this.parentElement, e.clientY);
    if (afterElement == null) {
      this.parentElement.appendChild(draggedElement);
    } else {
      this.parentElement.insertBefore(draggedElement, afterElement);
    }

    return false;
  }

  function handleDrop(e) {
    if (e.stopPropagation) {
      e.stopPropagation();
    }

    // Update order in selectedQuestions array
    const items = document.querySelectorAll(".paper-question-item");
    const newOrder = [];

    items.forEach((item) => {
      const id = item.dataset.id;
      const question = selectedQuestions.find((q) => q.id === id);
      if (question) {
        newOrder.push(question);
      }
    });

    selectedQuestions = newOrder;
    renderPaper();

    return false;
  }

  function handleDragEnd(e) {
    this.classList.remove("dragging");
  }

  function getDragAfterElement(container, y) {
    const draggableElements = [
      ...container.querySelectorAll(".paper-question-item:not(.dragging)"),
    ];

    return draggableElements.reduce(
      (closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;

        if (offset < 0 && offset > closest.offset) {
          return { offset: offset, element: child };
        } else {
          return closest;
        }
      },
      { offset: Number.NEGATIVE_INFINITY },
    ).element;
  }

  // ===== ACTION BUTTONS =====
  function initActionButtons() {
    // Remove buttons
    document.querySelectorAll(".q-remove").forEach((btn) => {
      btn.addEventListener("click", function () {
        const id = this.dataset.id;
        removeQuestionFromPaper(id);
      });
    });

    // Move up buttons
    document.querySelectorAll(".q-move-up").forEach((btn) => {
      btn.addEventListener("click", function () {
        const item = this.closest(".paper-question-item");
        const id = item.dataset.id;
        moveQuestion(id, -1);
      });
    });

    // Move down buttons
    document.querySelectorAll(".q-move-down").forEach((btn) => {
      btn.addEventListener("click", function () {
        const item = this.closest(".paper-question-item");
        const id = item.dataset.id;
        moveQuestion(id, 1);
      });
    });
  }

  function moveQuestion(id, direction) {
    const index = selectedQuestions.findIndex((q) => q.id === id);
    if (index === -1) return;

    const newIndex = index + direction;
    if (newIndex < 0 || newIndex >= selectedQuestions.length) return;

    // Swap
    const temp = selectedQuestions[index];
    selectedQuestions[index] = selectedQuestions[newIndex];
    selectedQuestions[newIndex] = temp;

    renderPaper();
  }

  // ===== UPDATE STATS =====
  function updateStats() {
    const total = selectedQuestions.length;
    const easy = selectedQuestions.filter(
      (q) => q.difficultyClass === "badge-easy",
    ).length;
    const medium = selectedQuestions.filter(
      (q) => q.difficultyClass === "badge-medium",
    ).length;
    const hard = selectedQuestions.filter(
      (q) => q.difficultyClass === "badge-hard",
    ).length;

    // Update stat values
    const statValues = document.querySelectorAll(".stat-value");
    if (statValues.length >= 4) {
      statValues[0].textContent = total;
      statValues[1].textContent = easy;
      statValues[2].textContent = medium;
      statValues[3].textContent = hard;
    }
  }

  // ===== SAVE EXAM =====
  window.saveExam = function () {
    if (selectedQuestions.length === 0) {
      window.adminUI.showToast("Vui lòng chọn ít nhất 1 câu hỏi", "warning");
      return;
    }

    const examTitle = document.querySelector(".exam-title-input").value.trim();
    if (!examTitle) {
      window.adminUI.showToast("Vui lòng nhập tên đề thi", "warning");
      document.querySelector(".exam-title-input").focus();
      return;
    }

    window.adminUI.showConfirm(
      "Lưu đề thi",
      `Bạn có chắc chắn muốn lưu đề thi "${examTitle}" với ${selectedQuestions.length} câu hỏi?`,
      function () {
        // Simulate saving
        const btn = document.querySelector(".header-right .btn-primary");
        btn.classList.add("loading");

        setTimeout(() => {
          btn.classList.remove("loading");
          window.adminUI.showToast("Đã lưu đề thi thành công!", "success");

          // Optionally redirect
          // window.location.href = 'baikiemtra.html';
        }, 1500);
      },
    );
  };

  // ===== SEARCH =====
  const searchInput = document.querySelector(".panel-search input");
  if (searchInput) {
    searchInput.addEventListener("input", function () {
      const query = this.value.toLowerCase();
      const items = document.querySelectorAll(".q-select-item");

      items.forEach((item) => {
        const text = item
          .querySelector(".q-content p")
          .textContent.toLowerCase();
        if (text.includes(query)) {
          item.style.display = "";
        } else {
          item.style.display = "none";
        }
      });
    });
  }

  // ===== INIT ON LOAD =====
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
