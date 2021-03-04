<?php
namespace General;

/**
 * <Interface> Used to generalize the file management in the server, it have the
 * basic functions of any file manager class.
 */
interface iFileReader{

    /**
     * Loads a file to the class with the interface implemented
     * @param string $file The path to the file that will be loaded
     * @param array $args The arguments to load, it's general to any class to
     *                    use and adapt your arguments, it can be used by any way.
     * @return void
     */
    public function load(string $file, array $args = []): void;

    /**
     * Unload any file loaded by the class. Removing it from the
     * attributes
     * @return void
     */
    public function dispose(): void;

    /**
     * Returns if the manager have a file loaded or not
     * @return bool
     */
    public function gotFile(): bool;

    /**
     * Writes the changes in the file loaded by the manager.
     * @return void
     */
    public function writeChanges(): void;

    /**
     * Reads the plain text content of the file loaded by the manager
     * @return string|null
     */
    public function getRawContent();
}
