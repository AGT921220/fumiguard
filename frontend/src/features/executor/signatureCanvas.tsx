import { useEffect, useRef, useState } from 'react'

export function SignatureCanvas({
  onChange,
}: {
  onChange: (dataUrl: string) => void
}) {
  const canvasRef = useRef<HTMLCanvasElement | null>(null)
  const [drawing, setDrawing] = useState(false)

  useEffect(() => {
    const c = canvasRef.current
    if (!c) return
    const ctx = c.getContext('2d')
    if (!ctx) return
    ctx.lineWidth = 2
    ctx.lineCap = 'round'
    ctx.strokeStyle = '#111'
  }, [])

  function pos(e: PointerEvent, canvas: HTMLCanvasElement) {
    const rect = canvas.getBoundingClientRect()
    return { x: e.clientX - rect.left, y: e.clientY - rect.top }
  }

  function exportData() {
    const c = canvasRef.current
    if (!c) return
    onChange(c.toDataURL('image/png'))
  }

  function clear() {
    const c = canvasRef.current
    if (!c) return
    const ctx = c.getContext('2d')
    if (!ctx) return
    ctx.clearRect(0, 0, c.width, c.height)
    exportData()
  }

  return (
    <div>
      <canvas
        ref={canvasRef}
        width={320}
        height={160}
        className="w-full rounded-lg border border-slate-200 bg-white"
        onPointerDown={(e) => {
          const c = canvasRef.current
          if (!c) return
          c.setPointerCapture(e.pointerId)
          setDrawing(true)
          const ctx = c.getContext('2d')
          if (!ctx) return
          const p = pos(e.nativeEvent, c)
          ctx.beginPath()
          ctx.moveTo(p.x, p.y)
        }}
        onPointerMove={(e) => {
          if (!drawing) return
          const c = canvasRef.current
          if (!c) return
          const ctx = c.getContext('2d')
          if (!ctx) return
          const p = pos(e.nativeEvent, c)
          ctx.lineTo(p.x, p.y)
          ctx.stroke()
        }}
        onPointerUp={() => {
          setDrawing(false)
          exportData()
        }}
      />
      <button
        type="button"
        className="mt-2 text-xs font-semibold text-slate-600 underline"
        onClick={clear}
      >
        Limpiar firma
      </button>
    </div>
  )
}

