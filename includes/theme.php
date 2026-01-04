<?php
/**
 * Theme Management System - Dark/Light Mode
 * نظام إدارة السمات - الوضع الداكن والفاتح
 */

class ThemeManager {

    const THEME_LIGHT = 'light';
    const THEME_DARK = 'dark';
    const COOKIE_NAME = 'betag_theme';
    const COOKIE_EXPIRY = 31536000; // 1 year

    /**
     * Get current theme from cookie or default
     */
    public static function getCurrentTheme(): string {
        return $_COOKIE[self::COOKIE_NAME] ?? self::THEME_LIGHT;
    }

    /**
     * Check if dark mode is active
     */
    public static function isDarkMode(): bool {
        return self::getCurrentTheme() === self::THEME_DARK;
    }

    /**
     * Get CSS variables for current theme
     */
    public static function getCssVariables(): string {
        return '
        <style>
            :root {
                /* Light Theme (Default) */
                --bg-primary: #ffffff;
                --bg-secondary: #f9fafb;
                --bg-tertiary: #f3f4f6;
                --bg-card: #ffffff;
                --bg-input: #ffffff;
                --bg-hover: #f3f4f6;

                --text-primary: #111827;
                --text-secondary: #4b5563;
                --text-tertiary: #6b7280;
                --text-muted: #9ca3af;
                --text-inverse: #ffffff;

                --border-primary: #e5e7eb;
                --border-secondary: #d1d5db;

                --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);

                --primary-50: #eff6ff;
                --primary-100: #dbeafe;
                --primary-500: #3b82f6;
                --primary-600: #2563eb;
                --primary-700: #1d4ed8;
                --primary-900: #1e3a8a;

                --success: #10b981;
                --warning: #f59e0b;
                --error: #ef4444;
                --info: #3b82f6;
            }

            [data-theme="dark"] {
                /* Dark Theme */
                --bg-primary: #111827;
                --bg-secondary: #1f2937;
                --bg-tertiary: #374151;
                --bg-card: #1f2937;
                --bg-input: #374151;
                --bg-hover: #374151;

                --text-primary: #f9fafb;
                --text-secondary: #e5e7eb;
                --text-tertiary: #d1d5db;
                --text-muted: #9ca3af;
                --text-inverse: #111827;

                --border-primary: #374151;
                --border-secondary: #4b5563;

                --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
                --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4);
                --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5);

                --primary-50: #1e3a5f;
                --primary-100: #1e40af;
                --primary-500: #3b82f6;
                --primary-600: #60a5fa;
                --primary-700: #93c5fd;
                --primary-900: #dbeafe;
            }

            /* Apply theme variables */
            body {
                background-color: var(--bg-secondary);
                color: var(--text-primary);
                transition: background-color 0.3s ease, color 0.3s ease;
            }

            .bg-white, .bg-card {
                background-color: var(--bg-card) !important;
            }

            .bg-gray-50 {
                background-color: var(--bg-secondary) !important;
            }

            .bg-gray-100 {
                background-color: var(--bg-tertiary) !important;
            }

            .text-gray-900, .text-gray-800 {
                color: var(--text-primary) !important;
            }

            .text-gray-700, .text-gray-600 {
                color: var(--text-secondary) !important;
            }

            .text-gray-500, .text-gray-400 {
                color: var(--text-tertiary) !important;
            }

            .border-gray-200, .border-gray-300 {
                border-color: var(--border-primary) !important;
            }

            .shadow-sm {
                box-shadow: var(--shadow-sm) !important;
            }

            .shadow, .shadow-md {
                box-shadow: var(--shadow-md) !important;
            }

            .shadow-lg {
                box-shadow: var(--shadow-lg) !important;
            }

            /* Input fields */
            input, select, textarea {
                background-color: var(--bg-input) !important;
                color: var(--text-primary) !important;
                border-color: var(--border-primary) !important;
            }

            input::placeholder, textarea::placeholder {
                color: var(--text-muted) !important;
            }

            /* Cards */
            .card-hover {
                background-color: var(--bg-card);
            }

            .card-hover:hover {
                background-color: var(--bg-hover);
            }

            /* Theme toggle button */
            .theme-toggle {
                cursor: pointer;
                padding: 0.5rem;
                border-radius: 0.5rem;
                transition: all 0.3s ease;
            }

            .theme-toggle:hover {
                background-color: var(--bg-hover);
            }

            .theme-toggle .sun-icon {
                display: none;
            }

            .theme-toggle .moon-icon {
                display: block;
            }

            [data-theme="dark"] .theme-toggle .sun-icon {
                display: block;
            }

            [data-theme="dark"] .theme-toggle .moon-icon {
                display: none;
            }
        </style>';
    }

    /**
     * Get JavaScript for theme toggle
     */
    public static function getJavaScript(): string {
        return "
        <script>
            // Theme Management
            const ThemeManager = {
                THEME_KEY: 'betag_theme',

                init() {
                    // Check for saved theme or system preference
                    const savedTheme = localStorage.getItem(this.THEME_KEY) || this.getSystemTheme();
                    this.setTheme(savedTheme);

                    // Listen for system theme changes
                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                        if (!localStorage.getItem(this.THEME_KEY)) {
                            this.setTheme(e.matches ? 'dark' : 'light');
                        }
                    });
                },

                getSystemTheme() {
                    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
                },

                setTheme(theme) {
                    document.documentElement.setAttribute('data-theme', theme);
                    localStorage.setItem(this.THEME_KEY, theme);

                    // Set cookie for server-side
                    document.cookie = `betag_theme=\${theme};path=/;max-age=31536000;SameSite=Lax`;
                },

                toggle() {
                    const current = document.documentElement.getAttribute('data-theme') || 'light';
                    const newTheme = current === 'light' ? 'dark' : 'light';
                    this.setTheme(newTheme);
                },

                getCurrentTheme() {
                    return document.documentElement.getAttribute('data-theme') || 'light';
                }
            };

            // Initialize on DOM ready
            document.addEventListener('DOMContentLoaded', () => {
                ThemeManager.init();
            });

            // Also run immediately to prevent flash
            ThemeManager.init();
        </script>";
    }

    /**
     * Get theme toggle button HTML
     */
    public static function getToggleButton(): string {
        return '
        <button onclick="ThemeManager.toggle()" class="theme-toggle text-gray-600 hover:text-primary-600" title="تغيير السمة">
            <i class="fa-solid fa-moon moon-icon text-xl"></i>
            <i class="fa-solid fa-sun sun-icon text-xl"></i>
        </button>';
    }
}
