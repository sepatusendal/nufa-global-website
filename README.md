# NUFA Global Education — the website

![NUFA Global Education homepage screenshot](.github/assets/website-screenshot.png)

Yes, that hero video autoplays. No, we will not apologize for it.

## What is this

The official marketing website for **NUFA Global Education** — English Camp, English Course, Immersion, Study Abroad, Native Speaker programs, and other ways to turn Indonesian students into certified Global Citizens™ (trademark pending, mostly in our hearts).

It is a static, no-nonsense, no-framework HTML/CSS/JS site. No React, no build step, no `node_modules` folder eating 400MB of your SSD for a landing page. Just files, doing their job, quietly, like a good intern.

## Tech stack (brace yourself)

- HTML
- CSS
- Vanilla JavaScript
- A single hero video that will outlive us all

That's it. That's the stack. No framework was harmed in the making of this website.

## Project structure

```
.
├── index.html                     # Beranda — where the magic (and the video) starts
├── about.html, career.html, ...   # the usual suspects
├── program-*.html                 # every program we sell, one page each
├── style.css                      # ~1500 lines of vibes
├── script.js                      # makes the dropdowns dropdown
├── assets/
│   ├── logo.png
│   ├── video/hero-loop.mp4        # the star of the show
│   └── gallery/                   # photos of very happy students
└── .cpanel.yml                    # the unsung hero that ships this to production
```

## Running it locally

You don't need `npm install`. You don't need a bundler. You need a browser and mild curiosity.

```bash
python3 -m http.server 8842
```

Then open `http://localhost:8842`. If it doesn't load, it's not the code, it's you (check the port, check the folder, breathe).

## How this thing gets to the internet

This repo auto-deploys to shared cPanel hosting via **Git Version Control**, because we don't have SSH access and FTP got personally offended and blocked our connection (`ETIMEDOUT`, we don't forgive, we don't forget).

The flow:

1. Edit files in VS Code like a normal person
2. `git add -A && git commit -m "your message" && git push`
3. In cPanel → **Git Version Control** → Manage → **Update from Remote** → **Deploy HEAD Commit**
4. `.cpanel.yml` copies the files into `public_html`, and the website updates itself like a well-behaved robot

No FTP. No drama (anymore). Just Git doing what Git does.

## Editing content (video, images, copy)

Everything lives in `assets/`. Swap a file, commit, push, deploy. That's the whole ceremony. You do not need to open cPanel's File Manager ever again unless you enjoy pain.

## A word on the Lorem Ipsum

If you see "Lorem ipsum dolor sit amet" anywhere on this site, that is not a bug, that is a placeholder patiently waiting for someone (probably you) to replace it with real content. It has been very patient. Please be kind to it.

## License

All rights reserved, all vibes immaculate. Please don't steal our hero video, it took a while to compress.
