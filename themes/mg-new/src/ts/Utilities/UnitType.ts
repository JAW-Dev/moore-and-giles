export type Unit = "em" | "rem" | "px" | "%";

export class UnitType {

    private _types: Unit[] = [];

    private _size: string = "";

    private _type: Unit = null;

    private _num: number = 0;

    constructor(size: string, types: Unit[] = ["em", "px", '%', "rem"]) {
        this.size = size;
        this.types = types;

        this.types.forEach((type) => {
            if(this.size.indexOf(type) !== -1) {
                this.type = type;
                this.num = parseFloat(this.size.split(type)[0]);
            }
        })
    }

    get size(): string {
        return this._size;
    }

    set size(value: string) {
        this._size = value;
    }

    get types(): Unit[] {
        return this._types;
    }

    set types(value: Unit[]) {
        this._types = value;
    }

    get type(): Unit {
        return this._type;
    }

    set type(value: Unit) {
        this._type = value;
    }

    get num(): number {
        return this._num;
    }

    set num(value: number) {
        this._num = value;
    }
}