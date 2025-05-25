#!/bin/bash

TREE_HEIGHT=$(printf "%.0f" "$(echo "$1" | cut -d'.' -f1)")
SNOW_WIDTH=$(printf "%.0f" "$(echo "$2" | cut -d'.' -f1)")

if [[ -z "$TREE_HEIGHT" || -z "$SNOW_WIDTH" || "$TREE_HEIGHT" -le 0 || "$SNOW_WIDTH" -le 0 ]]; then
  echo "Помилка: Параметри мають бути додатними числами." >&2
  exit 1
fi

if (( TREE_HEIGHT < 9 )); then
  echo "Помилка: TREE_HEIGHT має бути щонайменше 9." >&2
  exit 2
fi

if (( SNOW_WIDTH < 9 )); then
  echo "Помилка: SNOW_WIDTH має бути щонайменше 9." >&2
  exit 3
fi

draw_line() {
  local symbol=$1
  local count=$2
  local padding=$(( (SNOW_WIDTH - count) / 2 ))
  printf "%*s" "$padding" ""
  printf "%0.s$symbol" $(seq 1 "$count")
  echo
}

draw_top_tier() {
  values=(1 3 5 7 9)
  symbols=("*" "#" "*" "#" "*")
  for i in "${!values[@]}"; do
    draw_line "${symbols[$i]}" "${values[$i]}"
  done
}

draw_other_tier() {
  count=0
  while [ $count -lt 4 ]; do
    case $count in
      0|2) draw_line "#" $((3 + count * 2)) ;;
      1|3) draw_line "*" $((3 + count * 2)) ;;
    esac
    ((count++))
  done
}

draw_trunk() {
  until [ $count -eq 2 ]; do
    draw_line "#" 3
    ((count++))
  done
}

draw_snow_line() {
  for sym in $(seq 1 $SNOW_WIDTH); do
    printf "*"
  done
  echo
}

LAYERS=$(( (TREE_HEIGHT - 5) / 4 ))

draw_top_tier

for ((i=0; i<LAYERS; i++)); do
  draw_other_tier
done

count=0
draw_trunk
draw_snow_line
