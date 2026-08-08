# Modul Botani Phanerogamae - JSON

Struktur ini dibuat agar modul web mudah dirender seperti platform pembelajaran.

## File utama
- `modules.json`: indeks semua bab dan daftar lesson.
- `bab-01-...json` s.d. `bab-09-...json`: isi materi per bab.

## Struktur lesson
Setiap lesson memiliki:
- `id`
- `order`
- `title`
- `slug`
- `content_format`: `markdown`
- `content`: isi materi siap dirender dengan Markdown renderer
- `key_points`: poin ringkas untuk kotak rangkuman
- `source_sections`: nomor bagian pada buku sumber

## Contoh penggunaan JavaScript

```js
const res = await fetch('/data/modules.json');
const course = await res.json();

const firstChapterFile = course.chapters[0].file;
const chapterRes = await fetch(`/data/${firstChapterFile}`);
const chapter = await chapterRes.json();

console.log(chapter.lessons[0].title);
console.log(chapter.lessons[0].content);
```

Saran UI:
- Sidebar: `chapter.title` → daftar `lesson.title`
- Halaman materi: render `lesson.content` sebagai Markdown
- Kotak "Yang perlu diingat": render `lesson.key_points`
