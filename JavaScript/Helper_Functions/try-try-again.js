/**
 * Try to execute a function, and if it fails, try again after a delay.
 * Use exponential backoff to increase the delay between attempts.
 * @param {Function} fn
 * @param {Number} attempts 
 * @param {Number} delay 
 * @returns 
 */
async function try_try_again(fn, attempts = 5, delay = 500) {
    let currentDelay = delay;
    let lastError;

    for (let attempt = 0; attempt <= attempts; attempt++) {
        try {
            // Promise.resolve lets this support both sync and async functions.
            return await Promise.resolve(fn());
        } catch (error) {
            lastError = error;
            const attemptsLeft = attempts - attempt;

            if (attemptsLeft === 0) {
                break;
            }

            console.log(
                `Error occurred: ${error.message}. Retrying in ${currentDelay}ms... (${attemptsLeft} attempts left)`
            );

            await new Promise((resolve) => setTimeout(resolve, currentDelay));
            currentDelay *= 2;
        }
    }

    throw lastError;
}