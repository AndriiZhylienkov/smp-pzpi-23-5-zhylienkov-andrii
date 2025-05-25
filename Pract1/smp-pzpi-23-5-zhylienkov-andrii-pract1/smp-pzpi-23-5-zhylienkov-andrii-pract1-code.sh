#!/bin/bash

SNOW_WIDTH=$1

TRUNK_HEIGHT=2
TRUNK_WIDTH=3

draw_tier() {
  width=1
  chars=("*" "#" "*" "#" "*")
  for ((i=0; i<5; i++)); do
    padding=$(( (SNOW_WIDTH - width) / 2 ))
    printf "%*s" "$padding" ""
    printf "%0.s${chars[$i]}" $(seq 1 "$width")
    echo
    width=$((width + 2))
  done
}

draw_second_tier() {
  width=3
  chars=("#" "*" "#" "*")
for ((i=0; i<4; i++)); do
  padding=$(( (SNOW_WIDTH - width) / 2 ))
  printf "%*s" "$padding" ""
  printf "%0.s${chars[$i]}" $(seq 1 "$width")
  echo
  width=$((width + 2))
  done
}

draw_tier

draw_second_tier

for ((i = 0; i < TRUNK_HEIGHT; i++)); do
   padding=$(( (SNOW_WIDTH - TRUNK_WIDTH) / 2 ))
   printf "%*s" "$padding" ""
  printf "%0.s#" $(seq 1 "$TRUNK_WIDTH")
echo
done
printf "%0.s*" $(seq 1 "$SNOW_WIDTH")
echo
