<?php

namespace yacoubalhaidari\Telr\Support;

/**
 * Minimal, dependency-free array-to-XML serializer used for building the
 * <invoice> payload required by invoice_create.xml. Not a general-purpose
 * XML library — deliberately scoped to what the invoice API needs:
 * simple elements and repeated sibling elements (e.g. multiple <item>).
 */
class XmlArraySerializer
{
    /**
     * @param  array<string, mixed>  $data
     */
    public static function toXml(string $rootElement, array $data): string
    {
        $xml = new \SimpleXMLElement("<{$rootElement}/>");
        self::arrayToXml($data, $xml);

        return (string) $xml->asXML();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected static function arrayToXml(array $data, \SimpleXMLElement $xml): void
    {
        foreach ($data as $key => $value) {
            if (is_int($key)) {
                // Repeated element (e.g. multiple <item> under <details>):
                // caller is expected to have wrapped these as
                // ['item' => [...], 'item' => [...]] handled below instead.
                continue;
            }

            if (is_array($value) && self::isListOfAssociativeArrays($value)) {
                // e.g. 'item' => [ [...], [...] ] -> multiple <item> siblings
                foreach ($value as $item) {
                    $child = $xml->addChild($key);
                    self::arrayToXml($item, $child);
                }

                continue;
            }

            if (is_array($value)) {
                $child = $xml->addChild($key);
                self::arrayToXml($value, $child);

                continue;
            }

            if ($value === null) {
                continue;
            }

            $xml->addChild($key, htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8'));
        }
    }

    protected static function isListOfAssociativeArrays(array $value): bool
    {
        if ($value === [] || !array_is_list($value)) {
            return false;
        }

        return is_array($value[0]);
    }
}
