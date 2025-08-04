# Twinery activity

This activity allows you to implement single-file .html apps from https://twinery.org/.

To pass grades back to Moodle, your twinery must call

<script>
set backToMoodle to {
    score:23,
    type:"twine_result",
    feedback: "You are awesome"
}
window.parent.postMessage(State.variables.backToMoodle);
</script>

