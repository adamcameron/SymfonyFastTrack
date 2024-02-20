let chai = require("chai");
let expect = chai.expect;

describe("Test Node.js", () => {
    it("has the expected version",  () => {
        expect(process.version).to.be.match(/v19\.\d+\.\d+/);
    })
});
