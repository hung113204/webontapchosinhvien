document.addEventListener("DOMContentLoaded", function () {
    const card = document.getElementById("flashcard");
    const btnPrev = document.getElementById("fcPrev");
    const btnNext = document.getElementById("fcNext");
    const elCur = document.getElementById("fcCurrent");
    const elTotal = document.getElementById("fcTotal");

    // Dữ liệu flashcard từ Laravel (hoặc dùng mẫu nếu chưa có)
    const flashcards = window.flashcardData || [
        {
            q: "Stack là gì?",
            a: "Stack (ngăn xếp) là CTDL hoạt động theo nguyên tắc LIFO — Last In, First Out.",
        },
        {
            q: "Queue là gì?",
            a: "Queue (hàng đợi) là CTDL hoạt động theo nguyên tắc FIFO — First In, First Out.",
        },
        {
            q: "Tree là gì?",
            a: "Tree (cây) là CTDL phân cấp gồm các node liên kết, có 1 node gốc (root).",
        },
        {
            q: "Big O là gì?",
            a: "Big O Notation mô tả độ phức tạp thời gian/không gian của thuật toán theo kích thước đầu vào.",
        },
        {
            q: "Đồ thị là gì?",
            a: "Đồ thị (Graph) là CTDL gồm tập đỉnh (vertices) và tập cạnh (edges) kết nối các đỉnh.",
        },
    ];

    let current = 0;
    let isFlipped = false;

    function render() {
        const fc = flashcards[current];
        card.querySelector(".fc-question").textContent = fc.q;
        card.querySelector(".fc-answer").textContent = fc.a;
        elCur.textContent = current + 1;
        elTotal.textContent = flashcards.length;

        // Reset flip
        isFlipped = false;
        card.classList.remove("flipped");
    }

    // Click để lật thẻ
    card.addEventListener("click", function () {
        isFlipped = !isFlipped;
        card.classList.toggle("flipped", isFlipped);
    });

    btnPrev.addEventListener("click", function () {
        current = (current - 1 + flashcards.length) % flashcards.length;
        render();
    });

    btnNext.addEventListener("click", function () {
        current = (current + 1) % flashcards.length;
        render();
    });

    // Shuffle
    const btnShuffle = document.querySelector(".btn-shuffle");
    if (btnShuffle) {
        btnShuffle.addEventListener("click", function () {
            flashcards.sort(() => Math.random() - 0.5);
            current = 0;
            render();
        });
    }

    render();
});
