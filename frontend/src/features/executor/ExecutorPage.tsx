import { useMemo, useState } from 'react'
import { useParams } from 'react-router-dom'
import { ApiError } from '../../app/api'
import { useReadOnly } from '../../app/readOnly'
import { showError, showSuccess } from '../../app/toast'
import { Button } from '../../components/Button'
import { Card } from '../../components/Card'
import { Input } from '../../components/Input'
import {
  addChemical,
  captureSignature,
  checkIn,
  checkOut,
  finalize,
  saveChecklist,
  startReport,
  uploadEvidence,
} from './api'
import { SignatureCanvas } from './signatureCanvas'
import { apiBlob } from '../../app/api'

export function ExecutorPage() {
  const { workOrderId } = useParams()
  const woId = Number(workOrderId)
  const { isReadOnly, setReadOnly } = useReadOnly()

  const [coords, setCoords] = useState<{ lat: number; lng: number } | null>(null)
  const [checklistRaw, setChecklistRaw] = useState('[{"key":"ok","value":true}]')
  const [notes, setNotes] = useState('')
  const [chemName, setChemName] = useState('Gel X')
  const [chemQty, setChemQty] = useState('1.0')
  const [chemUnit, setChemUnit] = useState('l')
  const [signatureName, setSignatureName] = useState('Cliente')
  const [signatureRole, setSignatureRole] = useState('CLIENT')
  const [signatureData, setSignatureData] = useState('')
  const [reportId, setReportId] = useState<number | null>(null)

  const disabled = isReadOnly

  const checklist = useMemo(() => {
    try {
      const parsed = JSON.parse(checklistRaw)
      return Array.isArray(parsed) ? parsed : []
    } catch {
      return []
    }
  }, [checklistRaw])

  async function getLocation() {
    if (!navigator.geolocation) {
      showError(null, 'Geolocalización no disponible')
      return
    }
    navigator.geolocation.getCurrentPosition(
      (pos) => {
        setCoords({ lat: pos.coords.latitude, lng: pos.coords.longitude })
        showSuccess('Ubicación capturada')
      },
      () => showError(null, 'No se pudo obtener ubicación'),
      { enableHighAccuracy: true, timeout: 10000 },
    )
  }

  function gate403(e: unknown) {
    const err = e as ApiError
    if (err.status === 403) setReadOnly(true)
  }

  async function doStartReport() {
    try {
      const r = await startReport(woId)
      setReportId(r.id)
      showSuccess(`Reporte iniciado (#${r.id})`)
    } catch (e) {
      gate403(e)
      showError(e, 'No se pudo iniciar reporte')
    }
  }

  async function doFinalize() {
    try {
      const r = await finalize(woId)
      setReportId(r.id)
      showSuccess('Reporte finalizado')
    } catch (e) {
      gate403(e)
      showError(e, 'No se pudo finalizar')
    }
  }

  async function download(path: 'pdf' | 'certificate') {
    if (!reportId) return
    try {
      const { blob, filename } = await apiBlob(`/api/v1/reports/${reportId}/${path}`)
      const url = URL.createObjectURL(blob)
      const a = document.createElement('a')
      a.href = url
      a.download = filename || `${path}-${reportId}.pdf`
      a.click()
      URL.revokeObjectURL(url)
    } catch (e) {
      showError(e, 'No se pudo descargar')
    }
  }

  if (!Number.isFinite(woId)) {
    return (
      <div className="mx-auto max-w-5xl px-4 pb-24 pt-6">
        <Card>WorkOrder inválida.</Card>
      </div>
    )
  }

  return (
    <div className="mx-auto max-w-5xl px-4 pb-24 pt-6">
      <h1 className="text-xl font-bold tracking-tight">Ejecutor móvil</h1>
      <p className="mt-1 text-sm text-slate-600">WorkOrder #{woId}</p>

      <div className="mt-4 grid gap-3">
        <Card>
          <div className="text-sm font-semibold">1) Ubicación / Check-in-out</div>
          <div className="mt-3 grid gap-2">
            <Button onClick={getLocation} className="bg-slate-100 text-slate-900">
              Capturar ubicación
            </Button>
            <div className="text-xs text-slate-600">
              {coords ? `lat ${coords.lat.toFixed(6)}, lng ${coords.lng.toFixed(6)}` : '—'}
            </div>
            <div className="grid grid-cols-2 gap-2">
              <Button
                disabled={disabled || !coords}
                onClick={async () => {
                  try {
                    await checkIn(woId, coords!.lat, coords!.lng)
                    showSuccess('Check-in OK')
                  } catch (e) {
                    gate403(e)
                    showError(e, 'No se pudo hacer check-in')
                  }
                }}
              >
                Check-in
              </Button>
              <Button
                disabled={disabled || !coords}
                className="bg-emerald-600"
                onClick={async () => {
                  try {
                    await checkOut(woId, coords!.lat, coords!.lng)
                    showSuccess('Check-out OK')
                  } catch (e) {
                    gate403(e)
                    showError(e, 'No se pudo hacer check-out')
                  }
                }}
              >
                Check-out
              </Button>
            </div>
          </div>
        </Card>

        <Card>
          <div className="text-sm font-semibold">2) Reporte</div>
          <div className="mt-3 grid gap-2">
            <Button disabled={disabled} onClick={doStartReport}>
              Iniciar reporte
            </Button>
            <div className="text-xs text-slate-600">Report ID: {reportId ?? '—'}</div>
          </div>
        </Card>

        <Card>
          <div className="text-sm font-semibold">3) Checklist y notas</div>
          <div className="mt-3 grid gap-2">
            <label className="text-xs font-semibold text-slate-700">
              Checklist (JSON)
            </label>
            <textarea
              className="min-h-24 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs"
              value={checklistRaw}
              onChange={(e) => setChecklistRaw(e.target.value)}
              disabled={disabled}
            />
            <Input
              value={notes}
              onChange={(e) => setNotes(e.target.value)}
              placeholder="Notas"
              disabled={disabled}
            />
            <Button
              disabled={disabled}
              onClick={async () => {
                try {
                  await saveChecklist(woId, checklist, notes)
                  showSuccess('Checklist guardado')
                } catch (e) {
                  gate403(e)
                  showError(e, 'No se pudo guardar checklist')
                }
              }}
            >
              Guardar
            </Button>
          </div>
        </Card>

        <Card>
          <div className="text-sm font-semibold">4) Químicos</div>
          <div className="mt-3 grid gap-2">
            <Input value={chemName} onChange={(e) => setChemName(e.target.value)} disabled={disabled} />
            <div className="grid grid-cols-2 gap-2">
              <Input value={chemQty} onChange={(e) => setChemQty(e.target.value)} disabled={disabled} />
              <Input value={chemUnit} onChange={(e) => setChemUnit(e.target.value)} disabled={disabled} />
            </div>
            <Button
              disabled={disabled}
              onClick={async () => {
                try {
                  await addChemical(woId, chemName, Number(chemQty), chemUnit)
                  showSuccess('Químico agregado')
                } catch (e) {
                  gate403(e)
                  showError(e, 'No se pudo agregar químico')
                }
              }}
            >
              Agregar químico
            </Button>
          </div>
        </Card>

        <Card>
          <div className="text-sm font-semibold">5) Fotos</div>
          <div className="mt-3 grid gap-2">
            <input
              type="file"
              accept="image/*"
              capture="environment"
              disabled={disabled}
              onChange={async (e) => {
                const file = e.target.files?.[0]
                if (!file) return
                try {
                  await uploadEvidence(woId, file)
                  showSuccess('Evidencia subida')
                } catch (err) {
                  gate403(err)
                  showError(err, 'No se pudo subir evidencia')
                } finally {
                  e.target.value = ''
                }
              }}
            />
            <div className="text-xs text-slate-500">
              Se sube como evidencia al reporte (si ya está iniciado).
            </div>
          </div>
        </Card>

        <Card>
          <div className="text-sm font-semibold">6) Firma</div>
          <div className="mt-3 grid gap-2">
            <Input
              value={signatureName}
              onChange={(e) => setSignatureName(e.target.value)}
              disabled={disabled}
              placeholder="Firmante"
            />
            <Input
              value={signatureRole}
              onChange={(e) => setSignatureRole(e.target.value)}
              disabled={disabled}
              placeholder="Rol (CLIENT/TECHNICIAN)"
            />
            <SignatureCanvas onChange={(d) => setSignatureData(d)} />
            <Button
              disabled={disabled || !signatureData}
              onClick={async () => {
                try {
                  await captureSignature(woId, signatureName, signatureRole || null, signatureData)
                  showSuccess('Firma guardada')
                } catch (e) {
                  gate403(e)
                  showError(e, 'No se pudo guardar firma')
                }
              }}
            >
              Guardar firma
            </Button>
          </div>
        </Card>

        <Card>
          <div className="text-sm font-semibold">7) Finalizar + PDFs</div>
          <div className="mt-3 grid gap-2">
            <Button disabled={disabled} className="bg-indigo-600" onClick={doFinalize}>
              Finalizar reporte
            </Button>
            <div className="grid grid-cols-2 gap-2">
              <Button
                disabled={!reportId}
                className="bg-slate-100 text-slate-900"
                onClick={() => download('pdf')}
              >
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
        </Card>
      </div>
    </div>
  )
}

