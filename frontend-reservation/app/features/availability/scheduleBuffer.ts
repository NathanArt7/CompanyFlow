import type { ScheduleBlock } from './types'

// Le délai (reservation_buffer) sert à laisser le temps de vider/réaménager la salle : il
// s'applique uniquement APRÈS chaque réservation, jamais avant (la toute première
// réservation de la journée a donc un délai après elle, mais aucune n'a de délai avant).
export function buildBufferBlocks(
  reservedBlocks: Array<{ startMinutes: number, endMinutes: number }>,
  bufferMinutes: number,
  dayEndMinutes: number,
): ScheduleBlock[] {
  if (bufferMinutes <= 0 || reservedBlocks.length === 0) return []

  return reservedBlocks
    .map((block) => {
      const afterEnd = Math.min(block.endMinutes + bufferMinutes, dayEndMinutes)
      if (afterEnd <= block.endMinutes) return null

      return {
        startMinutes: block.endMinutes,
        endMinutes: afterEnd,
        status: 'buffer',
        title: 'Délai (réaménagement de la salle)',
      } satisfies ScheduleBlock
    })
    .filter((block): block is ScheduleBlock => block !== null)
}
