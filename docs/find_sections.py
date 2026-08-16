# -*- coding: utf-8 -*-
import sys
sys.stdout.reconfigure(encoding='utf-8')
from docx import Document

doc = Document('تطوير نظام ادارة المستشفى - كامل.docx')

for i, p in enumerate(doc.paragraphs):
    t = p.text.strip()
    if '12.7.17' in t or '12.7.18' in t or 'HmsFullDemoSeeder' in t or t.startswith('12.7.6'):
        print(f'{i:4d}| {p.style.name:20s}| {t[:90]}')
