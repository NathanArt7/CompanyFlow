export interface ExportColumn {
  label: string
  key: string
}

export type ExportRow = Record<string, string | number>

function timestamp(): string {
  const d = new Date()
  return `${d.getFullYear()}${String(d.getMonth() + 1).padStart(2, '0')}${String(d.getDate()).padStart(2, '0')}-${String(d.getHours()).padStart(2, '0')}${String(d.getMinutes()).padStart(2, '0')}`
}

export function useTableExport() {
  async function exportToPdf(filename: string, title: string, columns: ExportColumn[], rows: ExportRow[]) {
    const { jsPDF } = await import('jspdf')
    const autoTable = (await import('jspdf-autotable')).default

    const doc = new jsPDF({ orientation: columns.length > 5 ? 'landscape' : 'portrait' })

    // Titre souligné (jsPDF n'a pas de style "underline" natif : on trace la ligne à la main).
    doc.setFontSize(18)
    doc.setFont('helvetica', 'bold')
    doc.setTextColor(20)
    doc.text(title, 14, 18)
    doc.setLineWidth(0.4)
    doc.line(14, 19.5, 14 + doc.getTextWidth(title), 19.5)

    // "Date : " (label souligné) suivi de la date en écriture normale.
    const dateLabel = 'Date : '
    const dateValue = new Date().toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })
    doc.setFontSize(10)
    doc.setFont('helvetica', 'normal')
    doc.text(dateLabel, 14, 25)
    const dateLabelWidth = doc.getTextWidth(dateLabel)
    doc.line(14, 26, 14 + dateLabelWidth, 26)
    doc.text(dateValue, 14 + dateLabelWidth, 25)

    autoTable(doc, {
      startY: 31,
      head: [columns.map(col => col.label)],
      body: rows.map(row => columns.map(col => String(row[col.key] ?? ''))),
      theme: 'grid',
      styles: { fontSize: 8, textColor: 20, lineColor: 180, lineWidth: 0.1 },
      headStyles: { fillColor: false, textColor: 20, fontStyle: 'bold' },
    })

    doc.save(`${filename}-${timestamp()}.pdf`)
  }

  async function exportToExcel(filename: string, sheetName: string, columns: ExportColumn[], rows: ExportRow[]) {
    const XLSX = await import('xlsx')

    const data = rows.map(row => Object.fromEntries(columns.map(col => [col.label, row[col.key] ?? ''])))
    const worksheet = XLSX.utils.json_to_sheet(data)
    const workbook = XLSX.utils.book_new()
    XLSX.utils.book_append_sheet(workbook, worksheet, sheetName)

    XLSX.writeFile(workbook, `${filename}-${timestamp()}.xlsx`)
  }

  return { exportToPdf, exportToExcel }
}
