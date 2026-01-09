import { useState } from 'react'
import { apiBlob } from '../../app/api'
import { showError } from '../../app/toast'
import { Button } from '../../components/Button'
import { Card } from '../../components/Card'
import { Input } from '../../components/Input'

export function ReportsPage() {
  const [reportId, setReportId] = useState('')

  async function download(kind: 'pdf' | 'certificate') {
    try {
      const { blob, filename } = await apiBlob(`/api/v1/reports/${reportId}/${kind}`)
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = filename || `${kind}-${reportId}.pdf`
      a.click()
      URL.revokeObjectURL(url)
    } catch (e) {
      showError(e, 'No se pudo descargar')
    }
  }

  return (
    <div className="mx-auto max-w-5xl px-4 pb-24 pt-6">
      <h1 className="text-xl font-bold tracking-tight">Historial + PDFs</h1>
      <p className="mt-1 text-sm text-slate-600">
        Descarga PDFs por ID de ServiceReport (MVP).
      </p>

      <div className="mt-4 grid gap-3">
        <Card>
          <div className="text-sm font-semibold">Descargar</div>
          <div className="mt-3 grid gap-2">
            <Input
              inputMode="numeric"
              value={reportId}
              onChange={(e) => setReportId(e.target.value)}
              placeholder="ServiceReport ID"
            />
            <div className="grid grid-cols-2 gap-2">
              <Button disabled={!reportId} onClick={() => download('pdf')}>
                Reporte PDF
              </Button>
              <Button
                disabled={!reportId}
                className="bg-slate-100 text-slate-900"
                onClick={() => download('certificate')}
              >
                Certificado
              </Button>
            </div>
          </div>
          <div className="mt-2 text-xs text-slate-500">
            Nota: la lista de reportes (history) requiere un endpoint de listado; por ahora es
            descarga directa.
          </div>
        </Card>
      </div>
    </div>
  )
}

