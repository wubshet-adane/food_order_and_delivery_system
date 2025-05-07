    let lastScrollTop = 0;
    const topbar = document.querySelector(".topbarInHeader");

    window.addEventListener("scroll", function () {
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

        if (currentScroll < lastScrollTop) {
            // Scrolling up
            topbar.classList.add("scrolled-up");
        } else {
            // Scrolling down
            topbar.classList.remove("scrolled-up");
        }

        lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; // Prevent negative
    });
