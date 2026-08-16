# -*- coding: utf-8 -*-
import sys
sys.stdout.reconfigure(encoding='utf-8')
from docx import Document

doc = Document('تطوير نظام ادارة المستشفى - كامل.docx')

for i in range(880, 970):
    p = doc.paragraphs[i]
    t = p.text.strip()
    if t:
        print(f'{i:4d}| {p.style.name:20s}| {t[:90]}')
