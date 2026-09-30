#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
Libera un PDF con restricciones/cifrado (owner password / empty user password)
y escribe una copia sin /Encrypt para que Smalot/PdfParser pueda leerlo.

Uso:
  python unlock_pdf.py <entrada.pdf> <salida.pdf> [--password CLAVE]

Códigos de salida:
  0  OK
  1  error de uso / archivo
  2  no se pudo descifrar
  3  dependencia faltante (pypdf)
"""

from __future__ import annotations

import argparse
import sys
from pathlib import Path


def unlock_pdf(src: Path, dst: Path, password: str = "") -> None:
    try:
        from pypdf import PdfReader, PdfWriter
    except ImportError as exc:
        print("Falta pypdf. Instalá con: python -m pip install pypdf", file=sys.stderr)
        raise SystemExit(3) from exc

    if not src.is_file():
        print(f"No existe el PDF de entrada: {src}", file=sys.stderr)
        raise SystemExit(1)

    reader = PdfReader(str(src))
    if reader.is_encrypted:
        # Muchos extractos bancarios solo tienen owner-password; user vacío alcanza.
        candidates = []
        for cand in (password, "", None):
            if cand is None:
                continue
            if cand not in candidates:
                candidates.append(cand)
        if "" not in candidates:
            candidates.append("")

        unlocked = False
        last_err = None
        for cand in candidates:
            try:
                # pypdf: 0 = falló, >0 = OK (según versión puede devolver bool)
                result = reader.decrypt(cand)
                if result:
                    unlocked = True
                    break
            except Exception as exc:  # noqa: BLE001
                last_err = exc

        if not unlocked:
            msg = "No se pudo descifrar el PDF (contraseña incorrecta o cifrado no soportado)."
            if last_err is not None:
                msg += f" Detalle: {last_err}"
            print(msg, file=sys.stderr)
            raise SystemExit(2)

    writer = PdfWriter()
    for page in reader.pages:
        writer.add_page(page)

    # Copiar metadatos si existen
    if reader.metadata:
        try:
            writer.add_metadata(reader.metadata)
        except Exception:  # noqa: BLE001
            pass

    # Asegurar que la salida no quede cifrada
    try:
        writer.encrypt = None  # type: ignore[attr-defined]
    except Exception:  # noqa: BLE001
        pass

    dst.parent.mkdir(parents=True, exist_ok=True)
    with dst.open("wb") as fh:
        writer.write(fh)

    # Verificación rápida
    check = PdfReader(str(dst))
    if check.is_encrypted:
        print("La salida sigue marcada como cifrada.", file=sys.stderr)
        raise SystemExit(2)

    print(f"OK pages={len(check.pages)} out={dst}")


def main() -> None:
    parser = argparse.ArgumentParser(description="Quita cifrado/restricciones de un PDF.")
    parser.add_argument("input_pdf", help="PDF de entrada (puede estar protegido)")
    parser.add_argument("output_pdf", help="PDF de salida sin restricciones")
    parser.add_argument("--password", default="", help="Contraseña de usuario (opcional)")
    args = parser.parse_args()

    unlock_pdf(Path(args.input_pdf), Path(args.output_pdf), password=str(args.password or ""))


if __name__ == "__main__":
    main()
