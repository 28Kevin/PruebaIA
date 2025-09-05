<template>
  <div id="app">
    <router-view />
  </div>
</template>

<script setup lang="ts">
// Main app component - just renders the router view
</script>

<style>
html, body {
  margin: 0;
  padding: 0;
  height: 100%;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

#app {
  height: 100vh;
}
</style>
