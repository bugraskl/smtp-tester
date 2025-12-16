# 📧 SMTP Tester

A modern, multilingual SMTP server testing tool with detailed debug output.

![License](https://img.shields.io/badge/license-MIT-blue.svg)
![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4.svg)
![Languages](https://img.shields.io/badge/languages-21-green.svg)

## 🔗 Live Demo

**[Try it now → https://www.bugra.work/smtp-tester](https://www.bugra.work/smtp-tester)**

## ✨ Features

- **🌍 21 Languages** - Support for the world's most spoken languages with automatic browser detection
- **🔍 Detailed Debug Output** - Terminal-style step-by-step SMTP communication log
- **🔒 Secure Connections** - TLS/SSL encryption support
- **🎨 GitHub-Style UI** - Modern dark theme inspired by GitHub's design
- **📱 Responsive** - Works on desktop and mobile devices
- **⚡ Real-time** - AJAX-based instant results

## 🚀 Quick Start

### Requirements

- PHP 7.4 or higher
- Web server (Apache, Nginx, etc.)
- `sockets` PHP extension (usually enabled by default)

### Installation

1. Clone the repository:
```bash
git clone https://github.com/bugraskl/smtp-tester.git
```

2. Move to your web server directory:
```bash
cp -r smtp-tester /var/www/html/
# or for XAMPP on Windows:
# copy smtp-tester C:\xampp\htdocs\
```

3. Open in your browser:
```
http://localhost/smtp-tester/
```

## 📋 Usage

1. Enter your SMTP server details:
   - **Host**: Your SMTP server address (e.g., `smtp.gmail.com`)
   - **Port**: SMTP port (25, 465, or 587)
   - **Encryption**: None, TLS, or SSL
   - **Username/Password**: Optional authentication credentials

2. Click **Test SMTP Connection**

3. View the detailed debug output showing each step of the SMTP handshake

## 🌐 Supported Languages

| Language | Code | | Language | Code |
|----------|------|-|----------|------|
| 🇬🇧 English | en | | 🇻🇳 Tiếng Việt | vi |
| 🇨🇳 中文 | zh | | 🇹🇷 Türkçe | tr |
| 🇮🇳 हिन्दी | hi | | 🇵🇱 Polski | pl |
| 🇪🇸 Español | es | | 🇺🇦 Українська | uk |
| 🇸🇦 العربية | ar | | 🇳🇱 Nederlands | nl |
| 🇧🇩 বাংলা | bn | | 🇮🇩 Bahasa Indonesia | id |
| 🇧🇷 Português | pt | | 🇹🇭 ไทย | th |
| 🇷🇺 Русский | ru | | 🇮🇷 فارسی | fa |
| 🇯🇵 日本語 | ja | | | |
| 🇩🇪 Deutsch | de | | | |
| 🇫🇷 Français | fr | | | |
| 🇰🇷 한국어 | ko | | | |
| 🇮🇹 Italiano | it | | | |

## 📁 Project Structure

```
smtp-tester/
├── index.php              # Main application page
├── README.md              # This file
├── api/
│   └── test.php           # SMTP test API endpoint
├── assets/
│   ├── css/
│   │   └── style.css      # GitHub-style dark theme
│   └── js/
│       └── app.js         # Frontend JavaScript
├── includes/
│   ├── config.php         # Configuration
│   ├── functions.php      # Helper functions
│   └── SmtpTester.php     # SMTP testing class
└── languages/
    ├── en.php             # English
    ├── tr.php             # Turkish
    └── ...                # Other languages
```

## 🔧 Common SMTP Servers

| Provider | Host | Port | Encryption |
|----------|------|------|------------|
| Gmail | smtp.gmail.com | 587 | TLS |
| Outlook | smtp.outlook.com | 587 | TLS |
| Yahoo | smtp.mail.yahoo.com | 587 | TLS |
| Yandex | smtp.yandex.com | 465 | SSL |

> ⚠️ **Note**: For Gmail, you need to use an [App Password](https://support.google.com/accounts/answer/185833) if 2FA is enabled.

## 🤝 Contributing

Contributions are welcome! Feel free to:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Adding a New Language

1. Copy `languages/en.php` to `languages/xx.php` (where `xx` is the language code)
2. Translate all strings in the new file
3. Add the language to `$SUPPORTED_LANGUAGES` in `includes/config.php`

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- UI design inspired by [GitHub](https://github.com)
- Icons from native emoji set

---

Made with ❤️ by developers, for developers
