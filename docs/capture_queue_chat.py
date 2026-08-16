# -*- coding: utf-8 -*-
"""Capture doctor queue + chat screenshots with demo data."""
from pathlib import Path
from playwright.sync_api import sync_playwright

BASE = "http://127.0.0.1:8000"
DOCS = Path(__file__).resolve().parent
SHOTS = DOCS / "screenshots" / "doc"


def clean_ui(page) -> None:
    page.evaluate(
        """() => {
            document.querySelectorAll('.preloader, .hms-global-loader').forEach(el => el.remove());
        }"""
    )


def login_doctor(page) -> None:
    page.context.clear_cookies()
    page.goto(f"{BASE}/login", wait_until="domcontentloaded", timeout=30000)
    page.wait_for_selector("#sectionChooser", timeout=20000)
    clean_ui(page)
    page.select_option("#sectionChooser", "doctor")
    page.wait_for_timeout(400)
    page.locator("#doctor input[name='email']").fill("doctor@gmail.com")
    page.locator("#doctor input[name='password']").fill("12345678")
    page.locator("#doctor button[type='submit']").click()
    page.wait_for_load_state("domcontentloaded")
    page.wait_for_timeout(800)
    clean_ui(page)


def shot(page, name: str, full: bool = True) -> None:
    SHOTS.mkdir(parents=True, exist_ok=True)
    clean_ui(page)
    page.wait_for_timeout(500)
    page.screenshot(path=str(SHOTS / name), full_page=full)
    print("saved", SHOTS / name)


def main() -> None:
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page(viewport={"width": 1440, "height": 900})
        login_doctor(page)

        page.goto(f"{BASE}/doctor/queue", wait_until="domcontentloaded")
        page.wait_for_timeout(800)
        shot(page, "4-11-3-2-doctor-queue.png")

        page.goto(f"{BASE}/doctor/chat/patients", wait_until="domcontentloaded")
        page.wait_for_timeout(1200)
        try:
            first = page.locator(".hms-chat-list-item").first
            if first.count():
                first.click(timeout=5000)
                page.wait_for_timeout(1500)
        except Exception as exc:
            print("chat click:", exc)
        shot(page, "4-11-3-6-chat.png")

        browser.close()


if __name__ == "__main__":
    main()
